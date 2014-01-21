<?php

namespace VM\QuestionnaireBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use VM\QuestionnaireBundle\Form\QuestionnaireType;
use VM\QuestionnaireBundle\Entity\Questionnaire;
use VM\QuestionnaireBundle\Form\QuestionnaireElementType;
use VM\QuestionnaireBundle\Entity\QuestionnaireElement;
use VM\QuestionnaireBundle\Form\QuestionType;
use VM\QuestionnaireBundle\Entity\Question;
use VM\QuestionnaireBundle\Entity\QuestionnaireQuestionChoice;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;
use VM\GeneralBundle\Twig\VMExtension;

class FormController extends Controller {

    /**
     * Form action to choose Questionnaire process creation
     * @return \Symfony\Bundle\FrameworkBundle\Controller\Response
     */
    public function questionnaireCreationAction($slug_ent){

        $enterprise = $this->get('enterprise_repository')->getElements(array('action' => 'one', 'by_slug' => $slug_ent));

        return $this->render('VMQuestionnaireBundle:Form:questionnaire_creation.html.twig',array('enterprise' => $enterprise));
    }
    //Form action for Questionnaire
    public function questionnaireFormAction($slug_quest = NULL) {

        $request = $this->get('request');   
        $breadcrumbs = $this->get("white_october_breadcrumbs"); 
        
          
        $formConf = $this->get('form_model');
        
        //getting environment
        $env = $formConf->getEnv();
        
        //If session not set for Enterprise
        if($request->get('slug_ent')){  
               $entreprise = $this->get('enterprise_repository')->getElements(array('action'=>'one','by_slug'=>$request->get('slug_ent')));
               $enterprise = array('name' =>$entreprise->getName(),'slug' =>$entreprise->getSlug()) ;            
        }else{   
            if($env=='mo'){
              $access = $this->get('session')->get('access_admin');
              $enterprise = $access['current'];
            }
        }
        
        
        $formConf->setView('VMQuestionnaireBundle:Form:questionnaire_form.html.twig');
 
        //If backend process and enterprise is not given in url
        if($env=='bo' && isset($enterprise['slug'])){
            if( in_array($formConf->getAction(), array('edit', 'update'))){
                $formConf->setElement('questionnaire');
            }else{
                $formConf->setElement('enterprise_questionnaire');
            }
        }else{
            $formConf->setElement('questionnaire');
        }
               
        
        //If enterprise is given in url 
        if(!empty($enterprise) && array_key_exists('slug',$enterprise)){
            $formConf->setUrlParams(array('slug_ent' => $enterprise['slug'], 'slug_quest' => $slug_quest));
            $formConf->setFormClass('');

            // BREADCRUMBS          
            $breadcrumbs->addItem($enterprise['name'], $this->get("router")->generate($env."_enterprise_show", array('slug_ent' => $enterprise['slug'])));
            
            if($env=='mo')
               $breadcrumbs->addItem('Questionnaires', $this->get("router")->generate($env."_questionnaires", array('slug_ent' => $enterprise['slug'])));
            else
               $breadcrumbs->addItem('Questionnaires', $this->get("router")->generate($env."_questionnaires"));  
        }else{
             //if adding on index page of Backend   
             $formConf->setUrlParams(array('slug_quest' => $slug_quest));
             $breadcrumbs->addItem('Questionnaires', $this->get("router")->generate($env."_questionnaires"));  
        }
        
        // REDIRECTION PART
        if (in_array($formConf->getAction(), array('edit', 'update'))) {
            if ($slug_quest) {
                $object = $this->get('questionnaire_repository')->getElements(array('by_slug' => $slug_quest, 'action' => 'one'));
                if ($object) {
                    $formConf->setH1($this->get('translator')->trans('mo.questionnaire.title.edit', array('%quest%' => $object->getName())));
                    if($env=='mo')
                       $breadcrumbs->addItem($object->getName(), $this->get("router")->generate("mo_questionnaire_show", array('slug_ent' => $enterprise['slug'], 'slug_quest' => $object->getSlug())));
                    else
                        $breadcrumbs->addItem($object->getName(), $this->get("router")->generate("bo_questionnaire_show", array('slug_quest' => $object->getSlug()))); 
                    $breadcrumbs->addItem('Modifier');
                    
                    $formConf->setForm(new QuestionnaireType(), $object,array('formule'=>($object->getTextPayment()?2:1)));
                } 
            } else {
                
            }
        } else {
            $formConf->addGeneralHelp();
            $object = new Questionnaire();
             
            //If enterprise is given in url 
            if(isset($enterprise['slug'])){  
               $formConf->setH1($this->get('translator')->trans('mo.questionnaire.title.new', array('%ent%' => $enterprise['name'])));
            } else{
               $formConf->setH1($this->get('translator')->trans('bo.questionnaire.title.new'));
            }  
            
            $breadcrumbs->addItem('Nouveau'); 
           
             //If enterprise will given by autocomplete 
            if(!isset($enterprise['slug'])){                
                $formConf->setForm(new QuestionnaireType(), $object,array('no_enterprise'=>1));
            }else{
                $formConf->setForm(new QuestionnaireType(), $object);
            }
        }

        
        if ($this->get('request')->getMethod() == 'POST') {
            // Make Validation Part
            $params = array();
            $params['data'] = $this->get('request')->get('questionnaire');
             
            if(!isset($enterprise['slug']) && !in_array($formConf->getAction(), array('edit', 'update'))){
                $entreprise = $this->get('enterprise_repository')->getElements(array('action'=>'one','by_id'=>$params['data']['enterprise']['id']));
            }
            
            //If enterprise is given in url
            if(!isset($entreprise) && !in_array($formConf->getAction(), array('edit', 'update')))
                $entreprise = $this->get('enterprise_repository')->getElements(array('action'=>'one','by_slug'=>$enterprise['slug']));
            
            if(isset($entreprise)){
               $params['enterprise'] = $entreprise;
            }
            $params['env'] = $env;
           
            
            $params = $this->questionnaireProcessForm($formConf->getForm(), $formConf->getObject(), $params);


            // See if is success or not and redirect to the show page or return page with error
            if (isset($params['url_success'])) {
                if ($this->get('request')->isXmlHttpRequest()) {
                    return new Response($params['url_success']);
                } else {
                    return $this->redirect($params['url_success']);
                }
            }
            //if errors occuring
            if (isset($params['errors'])) {
                $formConf->setErrors($params['errors']);
            }
        }

        return $this->render($formConf->getTemplate(), $formConf->getParams());
    }

    //function to perform adding and updating a questionnaire
    private function questionnaireProcessForm($form, $obj, $params) {

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            //For payment related setting           
            if (isset($params['data']['formule']) && $params['data']['formule'] == 2) {
                
                if ($params['data']['payment_amount_before'] == '' || $params['data']['payment_amount_after'] == '' || $params['data']['text_payment'] == '') {
                    $params['errors'] = 'All fields related to payment are mandatory';
                    return $params;
                }
                
                $obj->setTextPayment($params['data']['text_payment'] );
                $obj->setPaymentAmountBefore($params['data']['payment_amount_before']);
                $obj->setPaymentAmountAfter($params['data']['payment_amount_after']);
                $obj->setPaymentVat($params['data']['payment_vat']);
                
                
                
            } else {
                $obj->setTextPayment('');
                $obj->setPaymentAmountBefore('');
                $obj->setPaymentAmountAfter('');
                $obj->setPaymentVat('');
            }

            //setting enterprise
            if(array_key_exists('enterprise', $params)){
               $obj->setEnterprise($params['enterprise']);
            }
            
            $em->persist($obj);
            $em->flush();
          
            //update indexation
            if($obj->getId() && $obj->getIsModel()== 1){
                  $duplicate = $this->get('duplicate_controller');
                  $duplicate->indexationQuestionnaire($obj);
            }
            
            if($params['env']=='mo')
              $params['url_success'] = $this->generateUrl('mo_questionnaire_elements', array('slug_ent' => $params['enterprise']->getSlug(), 'slug_quest' => $obj->getSlug()));
            else
              $params['url_success'] = $this->generateUrl('bo_questionnaire_elements', array('slug_quest' => $obj->getSlug()));
            
        } else {
            $params['errors'] = $form->getErrors();
        }

        return $params;
    }

    //Form action for Questionnaire element
    public function questionnaireElementFormAction($request = '') {
        $request = $request ? $request : $this->get('request');
       
        $reqQuestion = $request->get('Question');
        $element_type = $request->get('element_type')? $request->get('element_type'):($reqQuestion? $reqQuestion['QuestionnaireElement']['StdQuestionnaireTypeElement']:'');
        $quest_template = $request->get('question_type') ? $request->get('question_type') : ($reqQuestion ? $reqQuestion['StdQuestionType'] : '');

        $formConf = $this->get('form_model');
        $formConf->setFormClass('edit_question');
        $extraParams = array();
        
        $env = $formConf->getEnv();
       
        //if question element section 
        if ($element_type == 3) {            
            $parent_id = $request->get('parent_id') ? $request->get('parent_id') : (isset($reqQuestion['QuestionnaireElement']['parent_id']) ? $reqQuestion['QuestionnaireElement']['parent_id'] : '' );

            //if question type id is sent
            if ($quest_template) {
                $questTypeObj = $this->get('std_question_type_repository')->getElements(array('by_id' => $quest_template, 'action' => 'one'));
                $extraParams['questType'] = $questTypeObj;
            }
        } else {
            //for other element like group and description
            $quest_element = $request->get('QuestionnaireElement');
            $parent_id = $request->get('parent_id') ? $request->get('parent_id') : (isset($quest_element['parent_id']) ? $quest_element['parent_id'] : '' );
        }

                
        $formConf->setView('VMQuestionnaireBundle:Form:Element/element_form.html.twig');

        //if root element
        if (!$parent_id) {
             
            $formConf->setElement('questionnaire_element');
            if($env=='mo'){
               $formConf->setUrlParams(array('slug_ent' => $request->get('slug_ent'), 'slug_quest' => $request->get('slug_quest'), 'id' => $request->get('id')));
            }else{
                $formConf->setUrlParams(array('slug_quest' => $request->get('slug_quest'), 'id' => $request->get('id'))); 
            }    
        } else {
            // if sub element
            $formConf->setElement('questionnaire_element_child');
            
            if($env=='mo'){
                $formConf->setUrlParams(array('parent_id' => $parent_id, 'slug_ent' => $request->get('slug_ent'), 'slug_quest' => $request->get('slug_quest'), 'id' => $request->get('id')));
            }else{
                $formConf->setUrlParams(array('parent_id' => $parent_id,'slug_quest' => $request->get('slug_quest'), 'id' => $request->get('id')));
            }    
                
        }

        $formConf->setLayout(false);

        // REDIRECTION PART
        if (in_array($formConf->getAction(), array('edit', 'update'))) {
            if ($request->get('id')) {
                if ($element_type == 3) {
                    $object = $this->get('question_repository')->getElements(array('by_id' => $request->get('id'), 'action' => 'one'));
                    if(!$object->getQuestionnaireElement()->getParent()){
                        $extraParams['level_0'] = 1;
                        $extraParams['timeLimit'] = $object->getQuestionnaireElement()->getTimeLimit();
                    }
                    
                } else {
                    $object = $this->get('questionnaire_element_repository')->getElements(array('by_id' => $request->get('id'), 'action' => 'one'));
                }
             
            } else {
                
            }
        } else {
            //if question element 
            if ($element_type == 3) {
                $object = new Question();
            } else {
                $object = new QuestionnaireElement();
            }
            
            if(!$parent_id){
                $extraParams['level_0'] = 1;
            }    
        }

        //if question element 
        if ($element_type == 3) {
            
            $optionArray = array('question_type' => isset($questTypeObj) ? $questTypeObj : '');
            
            if (isset($questTypeObj) && $questTypeObj->getTemplate() == 'choice') {
                $choice_data = $this->get('questionnaire_question_choice_repository')->getElements(array('max_count' => 'id', 'action' => 'one'));
                $extraParams['countChoices'] = $choice_data['max_value'] + 1;
            }

            //for edit case of question
            if ($request->get('id')) {
                //for choice type question
                $extraParams['question'] = $object;
                $optionArray['other_options'] = array();

                
                if (isset($object->getOptions()['ChoiceType'])) {
                    $optionArray['choice_type'] = $object->getOptions()['ChoiceType'];
                    $extraParams['choiceType'] = $object->getQuestionnaireQuestionChoice();
                    $extraParams['choice_type'] = $optionArray['choice_type'];
                }

                
                //for datetime type question
                if (isset($object->getOptions()['DatetimeType'])) {
                    $optionArray['type_datetime'] = $object->getOptions()['DatetimeType'][0];
                    if(isset($object->getOptions()['DatetimeType']['format'])){
                       $optionArray['format'] = $object->getOptions()['DatetimeType']['format'];
                    }
                }
                //for evaluation type question
                if (isset($object->getOptions()['MediaType'])) {
                    $optionArray['doc_type'] = $object->getOptions()['MediaType']['format'];
                }
                
                //for evaluation type question
                if (isset($object->getOptions()['EvaluationType'])) {
                    $optionArray['evaluation_type'] = $object->getOptions()['EvaluationType'];
                }
                
                if ($object->getQuestionnaireElement()->getMediaType()) {                    
                    $optionArray['type_media'] = $object->getQuestionnaireElement()->getMediaType();  
                    $extraParams['type_media'] = $object->getQuestionnaireElement()->getMediaType();  
                }
                if ($object->getQuestionnaireElement()->getMediaAllow()) {    
                     $optionArray['allow_media'] = $object->getQuestionnaireElement()->getMediaAllow();  
                }
                //For setting otheroption so then corresponding checkbox can be checked on edit time
                if ($object->getRankin()) {
                    //$optionArray['other_options'][] = 'rankin';
                }
                /*if ($object->getNeeded()) {
                    $optionArray['other_options'][] = 'needed';
                }*/
                if ($object->getEliminateQuestion()) {
                    $optionArray['other_options'][] = 'eliminate_question';
                }
                if ($object->getAntiPlagiat()) {
                    $optionArray['other_options'][] = 'anti_plagiat';
                }
                
                $optionArray['webcam_show'] = 1 ;
                
                
            }else{ 
                
                //If parent then can not add webcam type question
                if($request->get('data_id') && $request->get('data_id') !='element_form'){
                    $optionArray['webcam_show'] = 0 ;
                }  
                
                if($request->get('id')){
                    if ($object->getMediaType()) {                    
                       $extraParams['type_media'] = $object->getMediaType();                      
                    }
                }
                
            }
            
             $formConf->setExtraParams($extraParams);
             $formConf->setForm(new QuestionType($this->getDoctrine()), $object, $optionArray);   
            
        } else {
            $optionsArray = array();
            if($request->get('id')){
                 $optionsArray['allow_media'] = $object->getMediaAllow();  
                 $optionsArray['type_media'] = $object->getMediaType();                 
                 $extraParams['type_media'] = $object->getMediaType();
                 $extraParams['element'] = $object;
            }
            
            $formConf->setExtraParams($extraParams);
            $formConf->setForm(new QuestionnaireElementType($optionsArray), $object);
        }

        if ($this->get('request')->getMethod() == 'POST') {

            // Make Validation Part
            $params = array();
            $params['slug_ent'] = $request->get('slug_ent');
            $params['questionnaire'] = $this->get('questionnaire_repository')->getElements(array('by_slug' => $request->get('slug_quest'), 'action' => 'one'));
            $params['element_type'] = $request->get('element_type');
            $params['env'] = $env;    
            //if not edit ot update action
            if (!$request->get('id')) {
                if (!$parent_id) {
                    //getting last position of root element                     
                    $questElem = $this->get('questionnaire_element_repository')->getElements(array('max_count' => 'position', 'by_questionnaire_id' => $params['questionnaire']->getId(), 'by_level' => 0, 'action' => 'one'));
                    $params['position'] = $questElem['max_value'] + 1;                    
                    
                } else {
                    $params['parent_id'] = $parent_id;
                    //getting parent element information
                    $params['parentElem'] = $this->get('questionnaire_element_repository')->getElements(array('by_id' => $parent_id, 'action' => 'one'));

                    //getting last position of parent subchild
                    $questElem = $this->get('questionnaire_element_repository')->getElements(array('max_count' => 'position', 'by_questionnaire_id' => $params['questionnaire']->getId(), 'by_level' => $params['parentElem']->getLevel() + 1, 'action' => 'one'));

                    if (isset($questElem['max_value'])) {
                        $params['position'] = $questElem['max_value'] + 1;
                    } else {
                        $params['position'] = 1;
                    }
                }
            }

            
            
           
            if ($request->get('element_type') && $request->get('Question')) {

                $params['question'] = 1;
                $questions = $request->get('Question');

                //if time_limit exists
                if(array_key_exists('time_limit',$questions['QuestionnaireElement'])){
                   $params['time_limit'] = $questions['QuestionnaireElement']['time_limit'];
                }
            
                //If time_limit exists
                if(array_key_exists('media_allow',$questions['QuestionnaireElement']) && $questions['QuestionnaireElement']['media_allow']==1){
                    $params['media_allow'] = 1;
                    if($questions['QuestionnaireElement']['media_type']=='image' || $questions['QuestionnaireElement']['media_type']=='video'){
                       $params['enclosed_file'] = $request->get('media_file');
                    }else if ($questions['QuestionnaireElement']['media_type']=='embed'){
                       $params['enclosed_file'] = $questions['QuestionnaireElement']['media_embed'];                      
                    }   
                     $params['media_type'] = $questions['QuestionnaireElement']['media_type'];
                }
                
                 //if choix type question setion
                $params['type_question'] = $questTypeObj->getTemplate();
                
                if(array_key_exists('QuestionOption',$questions)){
                      $QuestionOption = $questions['QuestionOption'];
                        //For other options like  eliminate_question, anti_plagiat, needed
                        if (array_key_exists('other_option',  $QuestionOption)) {
                            $params['other_option'] =  $QuestionOption['other_option'];
                        }
                        if (array_key_exists('needed',  $QuestionOption)) {
                            $params['needed'] =  $QuestionOption['needed'];
                        }
                        if (array_key_exists('eliminate_question',  $QuestionOption)) {
                            $params['eliminate_question'] =  $QuestionOption['eliminate_question'];
                        }
                        if (array_key_exists('char_limit',  $QuestionOption)) {
                            $params['char_limit'] =  $QuestionOption['char_limit'];
                        }

                        //If set char limit
                        if (array_key_exists('rankin',  $QuestionOption)) {
                            $params['rankin'] =  $QuestionOption['rankin'];
                        }
                        if($params['type_question']=='webcam'){
                            $params['response_time'] =  $QuestionOption['response_time'];
                            $params['question_time'] =  $QuestionOption['question_time'];
                        }
                        
                } 
                
               
                if ($params['type_question'] == 'choice') {
                    $params['questions']['choice_type'] = $questions['choice_type'];
                    $params['questions']['choice_name'] = $questions['choice_name'];
                    if(array_key_exists('ranking', $questions))
                        $params['questions']['ranking'] = $questions['ranking'];
                    
                    if(array_key_exists('total_ranking', $questions)){
                        $params['questions']['total_ranking'] = $questions['total_ranking'];
                    }
                    
                    /*if(array_key_exists('good_response', $questions)){
                        $params['questions']['good_response'] = $questions['good_response'];
                    }*/

                    // choix qcm type question
                    if ($questions['choice_type'] == 3) {
                        $params['questions']['good_response'] = isset($questions['good_response']) ? $questions['good_response'] : '';
                    }else if ($questions['choice_type'] == 4) {
                        $params['questions']['good_response'] = isset($questions['good_response_unique']) ? $questions['good_response_unique'] : '';
                    }/**/
                    //echo "<pre>";print_r($params['questions']);die;
                }

                // if date time question
                if ($params['type_question'] == 'datetime') {
                    $params['questions']['type_datetime'] = $questions['datetime_type'];
                    $params['questions']['format'] = $questions['format'];
                }

                //if evaluation type question
                if ($params['type_question'] == 'evaluation') {
                    $params['questions']['eval_type'] = $questions['evaluation_type'];
                }

                if($params['type_question'] == 'media') {
                    $params['questions']['doc_type'] = isset($questions['doc_type'])?$questions['doc_type']:'' ;
                }

                
            }else{
                //If time_limit exists
                if(!empty($quest_element) && array_key_exists('time_limit',$quest_element)){
                    $params['time_limit'] = $quest_element['time_limit'];
                }  
                
                //If time_limit exists
                if(!empty($quest_element) && array_key_exists('media_allow',$quest_element) && $quest_element['media_allow']!=''){
                    if($quest_element['media_type']=='image' || $quest_element['media_type']=='video'){
                       $params['enclosed_file'] = $request->get('media_file');
                      
                       
                    }else if ($quest_element['media_type']=='embed'){
                       $params['enclosed_file'] = $quest_element['media_embed'];
                    }   
                    
                     $params['media_type'] = $quest_element['media_type'];
                     $params['media_allow'] = $quest_element['media_allow'];
                }
               
            }
            
            $params = $this->questionnaireElementProcessForm($formConf->getForm(), $formConf->getObject(), $params);


            // See if is success or not and redirect to the show page or return page with error
            if (isset($params['url_success'])) {
                if ($this->get('request')->isXmlHttpRequest()) {
                    if($request->get('id')){ 
                        
                        if($request->get('element_type')==3){
                            $elements = $this->get('questionnaire_element_repository')->getElements(array('by_id' => $object->getQuestionnaireElement()->getId(), 'action' => 'one'));
                            return $this->render('VMQuestionnaireBundle:Middle:Element/question.html.twig', array('element'=>$elements));
                            
                        }else if($request->get('element_type')==2){
                            $elements = $this->get('questionnaire_element_repository')->getElements(array('by_id' => $request->get('id'), 'action' => 'one'));
                            return $this->render('VMQuestionnaireBundle:Middle:Element/description.html.twig', array('element'=>$elements));
                      
                        }else{
                            $elements = $this->get('questionnaire_element_repository')->getElements(array('by_id' => $request->get('id'), 'action' => 'one'));
                         
                            $childArray = array();
                            $subChildArray = array();

                            //sorting array of child with position for 3 level
                          
                            if (count($elements->getChildren()) > 0) {
                                foreach ($elements->getChildren() as $child) {

                                    $childArray[$elements->getId()][$child->getPosition()] = $child;

                                    //array for sub child for level 2
                                    if (count($child->getChildren()) > 0) {
                                        foreach ($child->getChildren() as $subchild) {
                                            $subChildArray[$child->getId()][$subchild->getPosition()] = $subchild;
                                        }
                                        ksort($subChildArray[$child->getId()]);
                                    }
                                }
                                ksort($childArray[$elements->getId()]);
                            }
                          

                            $type_elements = $this->get('std_questionnaire_type_element_repository');
                            $type_question = $this->get('std_question_type_repository');

                            $type_elements = $type_elements->getElements(array('action' => 'array'));
                            $type_elements[2]['type'] = $type_question->getElements(array('action' => 'array'));
                          
                            return $this->render('VMQuestionnaireBundle:Middle:Element/groupe.html.twig', array(
                                'element'=>$elements,
                                'childArray' => $childArray,
                                'subChildArray' => $subChildArray,
                                'type_elements' => $type_elements
                                ));
                        }
                                           
                    }else{
                         return new Response($params['url_success']);
                    }
                   
                } else {
                    return $this->redirect($params['url_success']);
                }
            }
            
            //if errors occuring
            if (isset($params['errors'])) {
                $formConf->setErrors($params['errors']);                
            }
        }
        
        return $this->render($formConf->getTemplate(), $formConf->getParams());
    }

    //function to perform adding and updating a questionnaire element
    private function questionnaireElementProcessForm($form, $obj, $params) {
       
        $type = 'element';
        //Error handling For question section for choice type 
        if (array_key_exists('element_type', $params) && $params['element_type'] == 3 && isset($params['question'])) {
             $type = 'question';
              //validation for choice type question  
              if (isset($params['type_question']) && $params['type_question'] == 'choice') {
                   $flag = 0;
                
                    if (isset($params['questions']['choice_name']) && count($params['questions']['choice_name'])>0) {                        
                        foreach($params['questions']['choice_name'] as $choice){
                            //if($choice !=''){
                                $flag=1; break;                               
                            //}
                        }
                        //If no choice name added 
                        if($flag==0){                            
                            $params['errors'] = "Atleast one choice is required ";
                            return $params;
                        } else{
                            $flagnew =0;    
                            //If QCM type question and no good response added
                            if (($params['questions']['choice_type'] == 3 || $params['questions']['choice_type'] == 4) && $params['questions']['good_response']=='') {
                                $params['errors'] = "Atleast one corresponding checkbox/radio is required";
                                return $params;
                            }
                            
                            if (($params['questions']['choice_type'] == 3 || $params['questions']['choice_type'] == 4) && $params['questions']['good_response']!='') {
                                foreach($params['questions']['good_response'] as $key=>$value ){
                                    if($params['questions']['choice_type'] == 4){
                                        if(isset($params['questions']['choice_name'][$value]) && $params['questions']['choice_name'][$value]!=''){
                                            $flagnew=1;
                                        }
                                    }else{
                                        if(isset($params['questions']['choice_name'][$key]) && $params['questions']['choice_name'][$key]!=''){
                                            $flagnew=1;
                                        }
                                    }                                    
                                }
                                 //If QCM type question and no good response added corresponding choice name
                                 if($flagnew==0){
                                      $params['errors'] = "Atleast one corresponding checkbox/radio is required";
                                      return $params;
                                 }
                            }                            
                            
                        }   
                    }                    
              }
              
              
              //Validation for media type question
              if (isset($params['type_question']) && $params['type_question'] == 'media') {
                  if($params['questions']['doc_type']==''){
                        $params['errors'] = " Atleast one document type required ";
                        return $params;
                  }
              }
              
        }
     
        if ($form->isValid()) {
            
            $em = $this->getDoctrine()->getManager();

            //For question Related section
            if (array_key_exists('element_type', $params) && $params['element_type'] == 3 && isset($params['question'])) {

                if (!$obj->getId()) {
                    $obj->getQuestionnaireElement()->setPosition($params['position']);
                }

                //setting questionnaire in question table
                $obj->getQuestionnaireElement()->setQuestionnaire($params['questionnaire']);
                //setParent Element
                if (array_key_exists('parentElem', $params) && $params['parentElem'] != '') {
                    $obj->getQuestionnaireElement()->setParent($params['parentElem']);
                }

                               
                //setting time limit
                if(array_key_exists('time_limit',$params)){
                    $obj->getQuestionnaireElement()->setTimeLimit($params['time_limit']);
                }else{
                     $obj->getQuestionnaireElement()->setTimeLimit(NULL);
                }
                
                
                //for setting response_time
                if (array_key_exists('response_time', $params) && $params['response_time'] != '') {
                    $obj->setResponseTime($params['response_time']);
                } else {
                    $obj->setResponseTime(NULL);
                }
                
                
               //for setting response_time
                if (array_key_exists('question_time', $params) && $params['question_time'] != '') {
                    $obj->setQuestionTime($params['question_time']);
                } else {
                    $obj->setQuestionTime(NULL);
                }
                
                //for setting char limit
                if (array_key_exists('char_limit', $params) && $params['char_limit'] != '') {
                    $obj->setCharLimit($params['char_limit']);
                } else {
                    $obj->setCharLimit(NULL);
                }
                
                //for setting char limit
                if (array_key_exists('rankin', $params) && $params['rankin'] != '') {
                    $obj->setRankin($params['rankin']);
                } else {
                    $obj->setRankin(NULL);
                }

                //for setting Needed
                if (array_key_exists('needed', $params) && $params['needed'] != '') {
                    $obj->setNeeded(1);
                } else {
                    $obj->setNeeded(NULL);
                }
                //for setting Eliminate Question
                if (array_key_exists('eliminate_question', $params) && $params['eliminate_question'] != '') {
                    $obj->setEliminateQuestion(1);
                } else {
                    $obj->setEliminateQuestion(NULL);
                }

                //for setting other options
                if (array_key_exists('other_option', $params) && !empty($params['other_option'])) {
                    //if checked anti_plagiat option
                    if (in_array('anti_plagiat', $params['other_option'])) {
                        $obj->setAntiPlagiat(1);
                    } else {
                        $obj->setAntiPlagiat(NULL);
                    }
                } else {
                    //$obj->setEliminateQuestion(NULL);
                    $obj->setAntiPlagiat(NULL);
                    //$obj->setNeeded(NULL);
                }

                //for setting choice type
                if (isset($params['type_question']) && ($params['type_question'] == 'choice')) {
                    $optionArr = array('ChoiceType' => $params['questions']['choice_type']);
                    
                                        
                    if(array_key_exists('total_ranking', $params['questions']) && $params['questions']['choice_type'] == 2){
                        $optionArr['total_ranking'] = $params['questions']['total_ranking'];
                    }
                    $obj->setOptions($optionArr);
                }
                //for setting datetime type
                if (isset($params['type_question']) && ($params['type_question'] == 'datetime')) {
                    if (isset($params['questions']['format']) && ($params['questions']['format'] != 'default')) {
                        $obj->setOptions(array('DatetimeType' => array(0 => $params['questions']['type_datetime'], 'format' => $params['questions']['format'])));
                    } else {
                        $obj->setOptions(array('DatetimeType' => array(0 => $params['questions']['type_datetime'])));
                    }
                }

                //for setting evaluation type
                if (isset($params['type_question']) && ($params['type_question'] == 'evaluation')) {
                    $obj->setOptions(array('EvaluationType' => $params['questions']['eval_type']));
                }

                 //for setting evaluation type
                if (isset($params['type_question']) && ($params['type_question'] == 'media')) {
                    
                    $media = array();
                    foreach($params['questions']['doc_type'] as $type){
                        $media[] =  $type;
                    }
                    
                    $obj->setOptions(array('MediaType' => array('format'=>$media)));
                }
                
                $obj->setHelp(NULL);
                $em->persist($obj);
                $em->flush();

                //for inserting choices in question choice table
                if (isset($params['type_question']) && $params['type_question'] == 'choice') {
                    if (isset($params['questions']['choice_name']) && count($params['questions']['choice_name']) > 0) {
                        $pos = 1;

                        $existsArray = array() ;
                        $question = Null; 
                        $question_id=0;
                        //Creating array of already exists choices
                        foreach ($obj->getQuestionnaireQuestionChoice() as $choix) {
                            if (array_key_exists($choix->getId(), $params['questions']['choice_name'])) {
                                $existsArray[] = $choix->getId();
                                $question_id = $choix->getQuestion()->getId();
                            }
                        }
                        if($question_id){
                            $question = $em->getRepository('VMQuestionnaireBundle:Question')->find($question_id);
                            foreach ($question->getQuestionnaireQuestionChoice() as $choiceObj){
                                if(!in_array($choiceObj->getId(), $existsArray)){
                                    $em->remove($choiceObj);
                                    $em->flush();
                                }
                            }
                        }
                        //Adding choices into choices table
                        foreach ($params['questions']['choice_name'] as $key => $name) {
                            
                            if ($name != '' || 1) {                                
                                if (!empty($existsArray) && in_array($key, $existsArray)) {
                                    $objChoice = $this->get('questionnaire_question_choice_repository')->getElements(array('by_id' => $key, 'action' => 'one'));
                                } else {                                                                        
                                    $objChoice = new QuestionnaireQuestionChoice();
                                }

                                $objChoice->setQuestion($obj);
                                $objChoice->setPosition($pos);
                                $objChoice->setName($name);
                                
                                if(array_key_exists('ranking', $params['questions']) && isset($params['questions']['ranking'][$key]) && $params['questions']['ranking'][$key]!=''){
                                    $objChoice->setRanking ($params['questions']['ranking'][$key]);
                                }else{
                                    $objChoice->setRanking (NULL);
                                }
                                    

                                //if qcm type given
                                if ($params['questions']['choice_type'] == 3 || $params['questions']['choice_type'] == 4) {
                                    //echo "<pre>";print_r($params['questions']['good_response']);
                                    if(array_key_exists('good_response', $params['questions'])){
                                        if (in_array($key, $params['questions']['good_response'])) {
                                            $objChoice->setGoodResponse(1);
                                        }else if (array_key_exists($key, $params['questions']['good_response'])) {
                                            $objChoice->setGoodResponse(1);
                                        }else {
                                            $objChoice->setGoodResponse(0);
                                        } 
                                    }else {
                                        $objChoice->setGoodResponse(0);
                                    }
                                    
                                } else {
                                    $objChoice->setGoodResponse(0);
                                }
                                $em->persist($objChoice);
                                $em->flush();
                                $pos++;
                            } else {
                                //if value was blank then remove
                                if (!empty($existsArray) && in_array($key, $existsArray)) {
                                    $objChoice = $this->get('questionnaire_question_choice_repository')->getElements(array('by_id' => $key, 'action' => 'one'));
                                    $em->remove($objChoice);
                                    $em->flush();
                                }
                            }
                        }
                    }
                }
                
            } else {
                //setting enterprise
                $obj->setQuestionnaire($params['questionnaire']);
                              
                
                //setting time limit
                if(array_key_exists('time_limit',$params)){
                  $obj->setTimeLimit($params['time_limit']);
                }
                
                //setParent Element
                if (array_key_exists('parentElem', $params) && $params['parentElem'] != '') {
                    $obj->setParent($params['parentElem']);
                }

                if (!$obj->getId()) {
                    $obj->setPosition($params['position']);
                }
                $em->persist($obj);
                $em->flush();
            }
            
            //update media of element  
            $this->updateElementMedia($obj , $params , $type );
            
            $em->refresh($obj);
            
            if($params['env']=='mo')
               $params['url_success'] = $this->generateUrl('mo_questionnaire_elements', array('slug_ent' => $params['slug_ent'], 'slug_quest' => $params['questionnaire']->getSlug()));
            else
               $params['url_success'] = $this->generateUrl('bo_questionnaire_elements', array('slug_quest' => $params['questionnaire']->getSlug())); 
            
        } else {
            
            $params['errors'] = $form->getErrors();
        }

        return $params;
    }
    
    //Form action for Questionnaire element
    public function questionnaireQuestionFormAction($request = '') {
        $request = $request ? $request : $this->get('request');
        $question_type = $request->get('question_type');

        //getting question type data 
        $questTypeObj = $this->get('std_question_type_repository')->getElements(array('by_id' => $question_type, 'action' => 'one'));
        $extraParams = array('withform' => false);

        if (isset($questTypeObj) && $questTypeObj->getTemplate() == 'choice') {
            $choice_data = $this->get('questionnaire_question_choice_repository')->getElements(array('max_count' => 'id', 'action' => 'one'));
            $extraParams['countChoices'] = $choice_data['max_value'] + 1;
        }
        
        if($request->get('data_id') && ( $request->get('data_id')== '' || $request->get('data_id') == 'element_form')){
            $extraParams['level_0'] = 1;
        }

        $extraParams['questType'] = $questTypeObj;
        $formConf = $this->get('form_model');
        $formConf->setExtraParams($extraParams);
        $formConf->setView('VMQuestionnaireBundle:Form:Element/Question/' . $questTypeObj->getTemplate() . '_form.html.twig');

        $formConf->setElement('questionnaire_element');
        $formConf->setUrlParams(array('slug_ent' => $request->get('slug_ent'), 'slug_quest' => $request->get('slug_quest'), 'id' => $request->get('id')));
      
        $formConf->setLayout(false);

        $object = new Question();
        $formConf->setForm(new QuestionType($this->getDoctrine()), $object, array('question_type' => $questTypeObj));

        return $this->render($formConf->getTemplate(), $formConf->getParams());
    }
    
    
    //For changing Status of quetsionnaire
    public function boChangeQuestionnaireStatusAction($slug_quest,$status) {
        $em = $this->getDoctrine()->getManager();
        $questionnaire = $this->get('questionnaire_repository')->getElements(array('by_slug' => $slug_quest, 'action' => 'one'));
        if ($questionnaire) {
            switch ($status){
                case "approuve":
                    $questionnaire->setApprobation(1);
                    break;
                case "disapprouve":
                    $questionnaire->setApprobation(0);
                    break;
                case "publish":
                    $questionnaire->setPublished(1);
                    break;
                case "depublish":
                    $questionnaire->setPublished(0);
                    break;
            }
            $em->persist($questionnaire);
            $em->flush();
        }
        return $this->redirect($this->getRequest()->headers->get('referer'));
    }
    
    
    /**
     * Questionnaire invitation form from questionnaire show page
     */

    public function moQuestionnaireInvitationAction($slug_quest = NULL) {
        
        $access = $this->get('session')->get('access_admin');
        $enterprise = $access['current'];       
        
        $questionnaire = $this->get('questionnaire_repository')->getElements(array('by_slug'=>$slug_quest,'action'=>'one'));
      
        if($questionnaire && !empty($enterprise) ){
            // BREADCRUMBS
            $breadcrumbs = $this->get("white_october_breadcrumbs");
            $breadcrumbs->addItem($questionnaire->getEnterprise()->getName(), $this->get("router")->generate("mo_enterprise_show", array('slug_ent' => $enterprise['slug'])));
            $breadcrumbs->addItem('Questionnaires', $this->get("router")->generate("mo_questionnaires", array('slug_ent' => $enterprise['slug'])));
           
            $breadcrumbs->addItem($questionnaire->getName(), $this->get("router")->generate("mo_questionnaire_show",array('slug_ent' => $enterprise['slug'],'slug_quest'=>$questionnaire->getSlug())));
             
            $breadcrumbs->addItem('Invitation');
                    
            $temp_params = array();
            if ($this->get('request')->getMethod() == 'POST') {
                $temp_params['form_data'] = $this->checkInvitationForm($this->getRequest()->get('email'));
                if($temp_params['form_data']['result']){
                    
                    if(array_key_exists('emails', $temp_params['form_data'])){
                        foreach($temp_params['form_data']['emails'] as $email){
                            $params = array('to' => $email,
                                'template' => '_questionnaireUserInvitation',
                                'temp_params' => array(                            
                                       'content'=>$questionnaire->getMailInvitation(),
                                       'link'=>'http://'.$this->getRequest()->getHttpHost().$this->generateUrl('fo_questionnaire_show',array('slug_quest'=>$slug_quest))));
                            $this->container->get('my_mailer')->sendMail($params);
                        }
                    }
                    
                    /*$this->get('my_mailer')->sendCustomEmails(array(
                           'to'=>$temp_params['form_data']['emails'],
                           'mail_subject'=>'Questionaire invitation',
                           'mail_template'=>'VMQuestionnaireBundle:Default:mail_invitation.html.twig',
                           'mail_temp_vars' => array(
                               'content'=>$questionnaire->getMailInvitation(),
                               'link'=>'http://'.$this->getRequest()->getHttpHost().$this->generateUrl('fo_questionnaire_show',array('slug_quest'=>$slug_quest)) )                               
                    ));  */          
                    $temp_params['success_msg'] = 'Questionaire invited successfully';
                    // See if is success or not and redirect to the show page or return page with error
                    if ($this->get('request')->isXmlHttpRequest()) {
                        //return new Response('url_success');
                    } else {
                        //return $this->redirect('url_success');
                    }
                }
            }
            
            $temp_params = array_merge($temp_params,array('slug_quest'=>$slug_quest, 'slug_ent'=>$enterprise['slug'], 'questionnaire' => $questionnaire));
            return $this->render('VMQuestionnaireBundle:Middle:questionnaire_invitation.html.twig', $temp_params);
        }
    }
    
    
    public function boQuestionnaireInvitationAction($slug_quest = NULL) {
        $questionnaire = $this->get('questionnaire_repository')->getElements(array('by_slug'=>$slug_quest,'action'=>'one'));
      
        if($questionnaire){
            // BREADCRUMBS
            $breadcrumbs = $this->get("white_october_breadcrumbs");            
            $breadcrumbs->addItem('Questionnaires', $this->get("router")->generate("bo_questionnaires"));
              
            $breadcrumbs->addItem($questionnaire->getName(), $this->get("router")->generate("bo_questionnaire_show",array('slug_quest'=>$questionnaire->getSlug())));
             
            $breadcrumbs->addItem('Invitation');
                    
            $temp_params = array();
            if ($this->get('request')->getMethod() == 'POST') {
                $temp_params['form_data'] = $this->checkInvitationForm($this->getRequest()->get('email'));
                if($temp_params['form_data']['result']){
                    
                    if(array_key_exists('emails', $temp_params['form_data'])){
                        foreach($temp_params['form_data']['emails'] as $email){
                            $params = array('to' => $email,
                                'template' => '_questionnaireUserInvitation',
                                'temp_params' => array(                            
                                       'content'=>$questionnaire->getMailInvitation(),
                                       'link'=>'http://'.$this->getRequest()->getHttpHost().$this->generateUrl('fo_questionnaire_show',array('slug_quest'=>$slug_quest))));
                            $this->container->get('my_mailer')->sendMail($params);
                        }
                    }
                    
                    
                    $temp_params['success_msg'] = 'Questionaire invited successfully';
                    // See if is success or not and redirect to the show page or return page with error
                    if ($this->get('request')->isXmlHttpRequest()) {
                        //return new Response('url_success');
                    } else {
                        //return $this->redirect('url_success');
                    }
                }
            }
            
            $temp_params = array_merge($temp_params,array('slug_quest'=>$slug_quest,'questionnaire'=>$questionnaire));
            return $this->render('VMQuestionnaireBundle:Back:questionnaire_invitation.html.twig', $temp_params);
        }
    }
    
    private function checkInvitationForm($emails = array()){
        
        $result = true;
        $errors = array();
        foreach($emails as $key=>$email){
            if(empty ($email)){
                $errors[$key] = 'Please enter email';$result = false;
            }else if(!preg_match('/^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/', $email)){
                $errors[$key] = 'Please enter valid email';$result = false;
            }else if(count(array_unique($emails)) != count($emails)){
                $errors['error_msg'] = 'Duplicate emails are not allowed';$result = false;
            }
        }        
        return array(
                        'emails'=>$emails,
                        'errors'=>$errors,
                        'result'=>$result,                        
                    );
    }
    
    
    public function questionnaireInvitationAction($slug_ent = NULL, $slug_quest) {
        $env = substr($this->get('request')->get('_route'), 0,3);
        $temp_params = array();
        if($slug_quest){
            $temp_params['route']['slug_quest'] =$slug_quest;
            $questionnaire = $this->get('questionnaire_repository')->getElements(array('by_slug'=>$slug_quest,'action'=>'one'));
            if($questionnaire){
                // BREADCRUMBS
                $breadcrumbs = $this->get("white_october_breadcrumbs");
                if($env == 'mo_'){
                    $access = $this->get('session')->get('access_admin');
                    $enterprise = $access['current']; 
                    $breadcrumbs->addItem($questionnaire->getEnterprise()->getName(), $this->get("router")->generate("mo_enterprise_show", array('slug_ent' => $enterprise['slug'])));
                    $breadcrumbs->addItem('Questionnaires', $this->get("router")->generate("mo_questionnaires", array('slug_ent' => $enterprise['slug'])));
                    $breadcrumbs->addItem($questionnaire->getName(), $this->get("router")->generate("mo_questionnaire_show",array('slug_ent' => $enterprise['slug'],'slug_quest'=>$questionnaire->getSlug())));
                    $temp_params['route']['slug_ent'] = $enterprise['slug'];
                    $template = 'Middle:questionnaire_invitation.html.twig';                    
                }else{
                    $breadcrumbs->addItem('Questionnaires', $this->get("router")->generate("bo_questionnaires"));              
                    $breadcrumbs->addItem($questionnaire->getName(), $this->get("router")->generate("bo_questionnaire_show",array('slug_quest'=>$questionnaire->getSlug())));
                    $template = 'Back:questionnaire_invitation.html.twig';
                }
                $breadcrumbs->addItem('Invitation');                
                $temp_params['questionnaire'] =$questionnaire;
                
                if ($this->get('request')->getMethod() == 'POST') {
                    $temp_params['form_data'] = $this->checkInvitationForm($this->getRequest()->get('email'));
                    if($temp_params['form_data']['result']){
                        if(array_key_exists('emails', $temp_params['form_data'])){
                            foreach($temp_params['form_data']['emails'] as $email){
                                $params = array('to' => $email,
                                    'template' => '_questionnaireUserInvitation',
                                    'temp_params' => array(                            
                                           'content'=>$questionnaire->getMailInvitation(),
                                           'link'=>'http://'.$this->getRequest()->getHttpHost().$this->generateUrl('fo_questionnaire_show',array('slug_quest'=>$slug_quest))));
                                $this->container->get('my_mailer')->sendMail($params);
                            }
                        }       
                        $this->get('session')->getFlashBag()->add('success','Emails invited successfully');
                        // See if is success or not and redirect to the show page or return page with error
                        if ($this->get('request')->isXmlHttpRequest()) {
                            return new Response($this->generateUrl($env.'questionnaire_invitation',$temp_params['route']));
                        }else {
                            return $this->redirect($this->generateUrl($env.'questionnaire_invitation',$temp_params['route']));
                        }
                    }
                }
                //echo $template;die;
                return $this->render('VMQuestionnaireBundle:'.$template, $temp_params);
            }else{
                $this->get('session')->getFlashBag()->add('error','Questionnaire not found.');
                return $this->redirect($this->generateUrl($env.'questionnaire_invitation',$temp_params['route']));
            }                        
        }else{
            $this->get('session')->getFlashBag()->add('error','Wrong parameters');
            return $this->redirect($this->getRequest()->headers->get('referer'));
        }       
    }

        //For changing status of respondant user
    public function questionnaireRespondantChangeStatusAction($questionnaire_user_id,$status){
        
        if($questionnaire_user_id){
            $questionnaire_user = $this->get('questionnaire_user_repository')->getElements(array('action' => 'one','by_id'=>$questionnaire_user_id));
            if($questionnaire_user){
                $em = $this->getDoctrine()->getManager();
                
                $params = array('to' => $questionnaire_user->getEmail());
                switch ($status){
                    case 'accepte':
                        $params['template'] = '_questionnaireUserAccept';
                        $params['temp_params'] = array('content'=>$questionnaire_user->getQuestionnaire()->getMailAccepted());
                        $questionnaire_user->setStatus(1);
                        break;
                    case 'refuse':
                        $params['template'] = '_questionnaireUserRefuse';
                        $params['temp_params'] = array('content'=>$questionnaire_user->getQuestionnaire()->getMailRefused());
                        $questionnaire_user->setStatus(2);
                        break;
                }
                $this->get('my_mailer')->sendMail($params);
                $em->persist($questionnaire_user);
                $em->flush();
                return $this->redirect($this->getRequest()->headers->get('referer'));
            }
        }
    }
    
   
    
    //Function to upload element media
    public function elementMediaUploadAction(){
        
        $request = $this->get('request');
        if($request->getMethod() == 'POST') {
		
                $error = ""; 
                //Get data of file uplaoded
                $data = $request->files->all(); 
                
                $fileElementName = isset($data['QuestionnaireElement']['element_media_file'])?$data['QuestionnaireElement']['element_media_file']:$data['Question']['QuestionnaireElement']['element_media_file'];
               
                $fileElementName->getClientOriginalName();                                  
                
                if(empty($fileElementName)){
                    echo json_encode( $fileElementName);
                }
                
                $type =  $request->get('type');
                $old_file =  $request->get('old_file');
                $elem_id =  $request->get('elem_id');
                
                //If file have error
                if(!empty($fileElementName) && $fileElementName->getError())
                {
                        switch($fileElementName->getError())
                        {
                                case '1':
                                        $error = 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
                                        break;
                                case '2':
                                        $error = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
                                        break;
                                case '3':
                                        $error = 'The uploaded file was only partially uploaded';
                                        break;
                                case '4':
                                        $error = 'No file was uploaded.';
                                        break;

                                case '6':
                                        $error = 'Missing a temporary folder';
                                        break;
                                case '7':
                                        $error = 'Failed to write file to disk';
                                        break;
                                case '8':
                                        $error = 'File upload stopped by extension';
                                        break;
                                case '999':
                                default:
                                        $error = 'No error code avaiable';
                        }
                        echo json_encode(array('error'=>$error));
                     
                }else{
                       //getting file info
                        $file_info = pathinfo($fileElementName->getClientOriginalName());
                        $ext = $file_info['extension'];
                                                 
                        //To check valid files for image
                        if($type=='image'){
                           if(!($ext=='jpg' || $ext=='jpeg' || $ext=='png' || $ext=='gif')){
                                echo json_encode(array('error'=>"Please upload valid file eg: png , jpeg , gif"));
                                exit;
                                
                           }
                        } 
                        //To check valid files for video
                        if($type=='video'){
                           if(!($ext=='mp4' || $ext=='flv')){
                                 echo json_encode(array('error'=>"Please upload valid file eg: mp4 ,flv "));
                                 exit;
                           }
                        }
                        
                        //get qeustionnaire information                        
                        $questionnaire = $this->get('questionnaire_repository')->getElements(array('by_slug'=>$request->get('slug_quest'),'action'=>'one'));
                       
                        //root path of project
                        $root_path = $this->getRequest()->server->get('DOCUMENT_ROOT');
       
                        $file_path = $root_path . '/uploads/questionnaire/'.$questionnaire->getId().'/files';

                        //creating folders if not exists
                        if (!is_dir($root_path . '/uploads')) {
                            mkdir($root_path . '/uploads', 0777);
                        }
                        if (!is_dir($root_path . '/uploads/questionnaire')) {
                            mkdir($root_path . '/uploads/questionnaire', 0777);
                        }
                        
                        if (!is_dir($root_path . '/uploads/questionnaire/'.$questionnaire->getId())) {
                            mkdir($root_path . '/uploads/questionnaire/'.$questionnaire->getId(), 0777);
                        }
                        
                        if (!is_dir($file_path)) {
                            mkdir($file_path, 0777);
                        }

                                                
                        //for edit case 
                        if($elem_id && $elem_id!=''){
                            $quest_element = $this->get('questionnaire_element_repository')->getElements(array('by_id'=>$elem_id,'action'=>'one'));
                            if($old_file !='' && ($quest_element->getEnclosedFiles()!=('/uploads/questionnaire/'.$questionnaire->getId().'/files/'.$old_file))){
                                //delete files
                                 $this->deleteUploadedDocument($file_path,$old_file);
                            }
                        }else{
                            //For add new 
                             //to remove old file if exists already
                            if($old_file!='' && file_exists($file_path.'/'.$old_file)){
                                  $this->deleteUploadedDocument($file_path,$old_file);
                            }
                        }
                        
                        //Adding all files like tiny , medium and big
                        $newfilename = $this->addUploadedDocument($fileElementName, $file_path ,$type);
                             
                        $result = array('result'=>$newfilename); 
                        echo json_encode($result);
                      
                }
                  exit;
        }
                
    }
    

   
    // Remove all repondant under the questionaire
    public function removeQuestionnaireUserAllAction($slug_quest){
        $questionnaire = $this->get('questionnaire_repository')->getElements(array('by_slug'=>$slug_quest,'action'=>'one'));
        if($questionnaire){
            $em = $this->getDoctrine()->getManager();
            foreach($questionnaire->getQuestionnaireUser() as $repondant){
                foreach($repondant->getQuestionnaireQuestionResponse() as $response){
                    $em->remove($response);
                }               
                $em->remove($repondant);
            }            
            $em->flush();
            return $this->redirect($this->getRequest()->headers->get('referer'));
        }else{
            echo 'Invalid argument passed.';exit;
        }
    }
    
    //Update element media 
    public function updateElementMedia($objElem , $params , $type){
        
        //This section is given for Element 
        $em = $this->getDoctrine()->getManager();
        if($type =='element')
          $newobj = $objElem;
        else                        
          $newobj = $objElem->getQuestionnaireElement();
                 
        $root_path = $this->getRequest()->server->get('DOCUMENT_ROOT'); 
            
            //To update enclosed file field inside element table
            if(array_key_exists('media_allow',$params )&& $params['media_allow']==1){
            
              if(array_key_exists('enclosed_file',$params ) && $params['enclosed_file']!=''){
                
                    //For video and images
                    if($params['media_type']=='image' || $params['media_type']=='video'){
                              $file_path = $root_path.'/uploads/questionnaire/'.$newobj->getQuestionnaire()->getId().'/files/';

                              //Remove First if already exists files
                              if($newobj->getEnclosedFiles() && $newobj->getEnclosedFiles()!='' && $newobj->getMediaType()!='embed'){

                                  $new_file_path = '/uploads/questionnaire/'.$newobj->getQuestionnaire()->getId().'/files/'.$params['enclosed_file'];

                                  if(file_exists($root_path.$newobj->getEnclosedFiles()) && $newobj->getEnclosedFiles()!= $new_file_path){
                                     //delete file
                                       $explode_path = explode('files/',$newobj->getEnclosedFiles());
                                       $file_path = $root_path.$explode_path[0].'files/';

                                      //delete old files
                                      $this->deleteUploadedDocument($file_path,$explode_path[1]);
                                      $new_file =  $newobj->getId().'_'.$params['enclosed_file'];

                                  }else if(!file_exists($root_path.$newobj->getEnclosedFiles()) && $newobj->getEnclosedFiles()!= $new_file_path){
                                      $new_file =  $newobj->getId().'_'.$params['enclosed_file'];
                                  }else{
                                      $new_file =  $params['enclosed_file'];
                                  }

                              }else{
                                  $new_file =  $newobj->getId().'_'.$params['enclosed_file'];
                              }

                              $file = '/uploads/questionnaire/'.$newobj->getQuestionnaire()->getId().'/files/'.$new_file;
                             
                            $this->renameUploadedDocument($file_path ,$params['enclosed_file'],$new_file );  

                            
                
                    }else{
                        $file = $params['enclosed_file'];
                        if($newobj->getMediaType()=='image' ||$newobj->getMediaType() == 'vedio' ){
                            if($newobj->getEnclosedFiles()!=''){
                                $explode_path = explode('files/',$newobj->getEnclosedFiles());
                                $file_path = $root_path.$explode_path[0].'files/';

                                //delete old files
                                $this->deleteUploadedDocument($file_path,$explode_path[1]);

                            }
                        }
                    }
                
               
               }else{
                    $file = NULL; 
                    
                    if($newobj->getEnclosedFiles()!=''){
                        $explode_path = explode('files/',$newobj->getEnclosedFiles());
                        $file_path = $root_path.$explode_path[0].'files/';

                        //delete old files
                        $this->deleteUploadedDocument($file_path,$explode_path[1]);

                    }
                  
               }
              
                //If elements 
                if($type=='element'){
                     $objElem->setEnclosedFiles($file);
                }else{
                    //If question
                    $objElem->getQuestionnaireElement()->setEnclosedFiles($file);
                } 
                $em->persist($objElem);
                $em->flush();
                
            }else{
                //If elements 
                //delete vide or image from uploads folders
                if($newobj->getMediaType()=='image' ||$newobj->getMediaType() == 'vedio' ){
                    if($newobj->getEnclosedFiles()!=''){
                        $explode_path = explode('files/',$newobj->getEnclosedFiles());
                        $file_path = $root_path.$explode_path[0].'files/';
                       
                        //delete old files
                        $this->deleteUploadedDocument($file_path,$explode_path[1]);
                         
                    }
                }
                
                if($type=='element'){
                        $objElem->setMediaAllow(0);
                        $objElem->setEnclosedFiles(NULL);
                        $objElem->setMediaType(NULL);
                }else{
                    //If question
                     $objElem->getQuestionnaireElement()->setMediaAllow(0);
                     $objElem->getQuestionnaireElement()->setEnclosedFiles(NULL);
                     $objElem->getQuestionnaireElement()->setMediaType(NULL);
                }
                
                $em->persist($objElem);
                $em->flush();
            }
    }
    
    
    //Function delete files from particular folder
    public function deleteUploadedDocument($path , $filename){
        //getting extension and file name
        $file_info = pathinfo($filename);
        $extension = $file_info['extension'];
        $file =$file_info['filename'];

         //to remove old file if exists already
        if(file_exists($path.'/'.$file. '.' . $extension)){
              unlink($path.'/'.$file. '.' . $extension);                                     
        }   
        if (file_exists($path .'/'. $file . '_tiny.' . $extension)) {
               unlink($path . '/'.$file . '_tiny.' . $extension);
        }
        if (file_exists($path .'/'. $file . '_small.' . $extension)) {
           unlink($path .'/'. $file . '_small.' . $extension);
        }

        if (file_exists($path .'/'.$file . '_medium.' . $extension)) {
           unlink($path .'/'. $file . '_medium.' . $extension);
        }
        if (file_exists($path .'/'. $file . '_big.' . $extension)) {
           unlink($path .'/' .$file . '_big.' . $extension);
        }
    }
    
    //Function delete files from particular folder
    public function addUploadedDocument($fileObj, $path , $type='image'){
        //getting extension and file name
        $file_info = pathinfo($fileObj->getClientOriginalName());
        $extension = $file_info['extension'];
        $filename =$file_info['filename'];
        
        $extensionObj = new VMExtension();
        
        //original name of file        
        $file_slugify = $extensionObj->slugify($filename);
        
        //for security reason, we force to remove all uploaded file
        //If not exists file then add
        if (file_exists($path.'/'.$file_slugify.'.' . $extension)) {
             $this->deleteUploadedDocument($path,$file_slugify.'.' . $extension);
        }          
        
        $fileObj->move($path,$file_slugify.'.' . $extension);  
        
        if($type=='image'){
            //this adding 4 other size files
            $src = $path .'/'. $file_slugify . '.' . $extension;

            $dst = $path .'/'. $file_slugify . '_tiny.' . $extension;

            $extensionObj->image_resize($src, $dst, 70);

            $dst = $path .'/'. $file_slugify . '_small.' . $extension;
            $extensionObj->image_resize($src, $dst, 100);
            $dst = $path .'/'.$file_slugify . '_medium.' . $extension;
            $extensionObj->image_resize($src, $dst, 150);
            $dst = $path .'/'. $file_slugify . '_big.' . $extension;
            $extensionObj->image_resize($src, $dst, 250);
        }
        return ($file_slugify.'.' . $extension);
    }
    
    //Function renames files from particular folder
    public function renameUploadedDocument($path , $src , $dst){
        
        $srcFileInfo = pathinfo($src);
        $srcExt = $srcFileInfo['extension'];
        $srcFile = $srcFileInfo['filename'];  

        $dstFileInfo = pathinfo($dst);
        $dstExt = $dstFileInfo['extension'];
        $dstFile = $dstFileInfo['filename'];  

        // rename old file with new one for all size file  
        if(file_exists($path.$src)){
           rename($path.$src , $path.$dst);                               
        }

        //rename tiny file
        if(file_exists($path.$srcFile.'_tiny.'.$srcExt)){
           rename($path.$srcFile.'_tiny.'.$srcExt , $path.$dstFile.'_tiny.'.$dstExt);                               
        }
        //rename small file
        if(file_exists($path.$srcFile.'_small.'.$srcExt)){
           rename($path.$srcFile.'_small.'.$srcExt , $path.$dstFile.'_small.'.$dstExt);                               
        }
        //rename medium file 
        if(file_exists($path.$srcFile.'_medium.'.$srcExt)){
           rename($path.$srcFile.'_medium.'.$srcExt ,$path.$dstFile.'_medium.'.$dstExt);                               
        }
        //rename big file
        if(file_exists($path.$srcFile.'_big.'.$srcExt)){
           rename($path.$srcFile.'_big.'.$srcExt , $path.$dstFile.'_big.'.$dstExt);                               
        }

    }
    
    
}



<?php

namespace VM\QuestionnaireBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ElementController extends Controller {

    public function moShowAction() {
        $request = $this->get('request');
        $slug_quest = $request->get('slug_quest');
        $slug_ent = $request->get('slug_ent');

        //Questionnaire information
        $questionnaire = $this->get('questionnaire_repository')->getElements(array('by_slug' => $slug_quest, 'action' => 'one'));

        //elements list
        $elements = $this->get('questionnaire_element_repository')->getElements(array('by_questionnaire_id' => $questionnaire->getId(), 'by_level' => 0, 'order_by' => array('field' => 'position', 'sort' => 'asc')));

        $childArray = array();
        $subChildArray = array();

        //sorting array of child with position for 3 level
        foreach ($elements as $elem) {
            if (count($elem->getChildren()) > 0) {
                foreach ($elem->getChildren() as $child) {

                    $childArray[$elem->getId()][$child->getPosition()] = $child;

                    //array for sub child for level 2
                    if (count($child->getChildren()) > 0) {
                        foreach ($child->getChildren() as $subchild) {
                            $subChildArray[$child->getId()][$subchild->getPosition()] = $subchild;
                        }
                        ksort($subChildArray[$child->getId()]);
                    }
                }
                ksort($childArray[$elem->getId()]);
            }
        }
        $type_elements = $this->get('std_questionnaire_type_element_repository');
        $type_question = $this->get('std_question_type_repository');
  
        $type_elements = $type_elements->getElements(array('action' => 'array', 'by_is_active' => true));
        $type_elements[2]['type'] = $type_question->getElements(array('action' => 'array', 'by_is_active' => true));
        
        // BREADCRUMBS
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem($questionnaire->getEnterprise()->getName(), $this->get("router")->generate("mo_enterprise_show", array('slug_ent' => $slug_ent)));
        $breadcrumbs->addItem('Questionnaires', $this->get("router")->generate("mo_questionnaires", array('slug_ent' => $slug_ent)));
        $breadcrumbs->addItem($questionnaire->getName(), $this->get("router")->generate("mo_questionnaire_show", array('slug_ent' => $slug_ent, 'slug_quest' => $slug_quest)));
        $breadcrumbs->addItem("Elements");

        return $this->render('VMQuestionnaireBundle:Middle:Element/elements_show.html.twig', array(
                    'questionnaire' => $questionnaire,
                    'elements' => $elements,
                    'childArray' => $childArray,
                    'subChildArray' => $subChildArray,
                    'type_elements' => $type_elements,
        ));
    }

    public function boShowAction() {
        $request = $this->get('request');
        $slug_quest = $request->get('slug_quest');
        
        
        //Questionnaire information
        $questionnaire = $this->get('questionnaire_repository')->getElements(array('by_slug' => $slug_quest, 'action' => 'one'));
        if($questionnaire->getEnterprise()){       
        //elements list
        $elements = $this->get('questionnaire_element_repository')->getElements(array('by_questionnaire_id' => $questionnaire->getId(), 'by_level' => 0, 'order_by' => array('field' => 'position', 'sort' => 'asc')));

        $childArray = array();
        $subChildArray = array();

        //sorting array of child with position for 3 level
        foreach ($elements as $elem) {
            if (count($elem->getChildren()) > 0) {
                foreach ($elem->getChildren() as $child) {

                    $childArray[$elem->getId()][$child->getPosition()] = $child;

                    //array for sub child for level 2
                    if (count($child->getChildren()) > 0) {
                        foreach ($child->getChildren() as $subchild) {
                            $subChildArray[$child->getId()][$subchild->getPosition()] = $subchild;
                        }
                        ksort($subChildArray[$child->getId()]);
                    }
                }
                ksort($childArray[$elem->getId()]);
            }
        }
        $type_elements = $this->get('std_questionnaire_type_element_repository');
        $type_question = $this->get('std_question_type_repository');

        $type_elements = $type_elements->getElements(array('action' => 'array'));
        $type_elements[2]['type'] = $type_question->getElements(array('action' => 'array'));

        // BREADCRUMBS
        $breadcrumbs = $this->get("white_october_breadcrumbs");        
        $breadcrumbs->addItem('Questionnaires', $this->get("router")->generate("bo_questionnaires"));
        $breadcrumbs->addItem($questionnaire->getName(), $this->get("router")->generate("bo_questionnaire_show", array('slug_quest' => $slug_quest)));
        $breadcrumbs->addItem("Elements");

        return $this->render('VMQuestionnaireBundle:Back:Element/elements_show.html.twig', array(
                    'questionnaire' => $questionnaire,
                    'elements' => $elements,
                    'childArray' => $childArray,
                    'subChildArray' => $subChildArray,
                    'type_elements' => $type_elements,
        ));
        
        }else{
            return $this->redirect($this->generateUrl('bo_questionnaire_show',array('slug_quest'=>$slug_quest)));
        }
    }

    //function for repositioning Element Position
    public function questionnaireElementConfigFormAction($slug_ent, $slug_quest, $id) {
        $element = $this->get('questionnaire_element_repository')->getElements(array('by_id' => $id, 'action' => 'one'));
        $updateLink = $this->get('router')->generate('mo_questionnaire_element_update_ajax', array('id' => $id, 'slug_ent' => $slug_ent, 'slug_quest' => $slug_quest));
        return $this->render('VMQuestionnaireBundle:Form:Element/element_option_form.html.twig', array('element' => $element, 'link' => $updateLink));
    }

    //function for repositioning Element Position
    public function questionnaireElementAjaxSaveAction($slug_ent, $slug_quest, $id) {
        $request = $this->get('request');
        $em = $this->getDoctrine()->getManager();
        $updateLink = $this->get('router')->generate('mo_questionnaire_element_update_ajax', array('id' => $id, 'slug_ent' => $slug_ent, 'slug_quest' => $slug_quest));
        $element = $this->get('questionnaire_element_repository')->getElements(array('by_id' => $id, 'action' => 'one'));

        switch ($request->request->get('field-type')) { // on indique sur quelle variable on travaille
            case 'title': // dans le cas où $note vaut 0
                $element->setName($request->request->get('value'));
                echo '<p  class="detect" data-type="title" onclick="getForm(\'' . $updateLink . '\', this)">' . $element->getName() . '</p>';
                break;
            case 'titleGroup': // dans le cas où $note vaut 5
                $element->setName($request->request->get('value'));
                echo '<p  class="detect" data-type="titleGroup" onclick="getForm(\'' . $updateLink . '\', this)">' . $element->getName() . '</p>';
                break;
            case 'time_limit': // dans le cas où $note vaut 5
                $element->setTimeLimit($request->request->get('value'));
                break;
            case 'description': // dans le cas où $note vaut 5
                $element->setTextDescription($request->request->get('value'));
                echo '<div class="detect no_bold italic textMCE" id="element_description" data-type="description" onclick="getForm(\'' . $updateLink . '\', this)">' . nl2br($element->getTextDescription()) . '</div>';
                break;
        }
        $em->persist($element);
        $em->flush();
        exit;
    }

    //function for repositioning Element Position
    public function questionnaireResponseAjaxSaveAction($slug_ent, $slug_quest, $repondant_id, $question_id) {
        $request = $this->get('request');
        $em = $this->getDoctrine()->getManager();
        $responses = $this->get('questionnaire_question_response_repository')->getElements(array('by_repondant_id' => $repondant_id, 'by_question_id' => $question_id, 'action' => 'execute'));
        
        $note = 0;
        
        if ($request->request->get('value') == 0 || $request->request->get('value')) {
            foreach ($responses as $response) {

                if ($response->getQuestionMark())
                    $mergedArray = array($this->getUser()->getId() => $request->request->get('value')) + $response->getQuestionMark();
                else
                    $mergedArray = array($this->getUser()->getId() => $request->request->get('value'));
                $response->setQuestionMark($mergedArray);
                $em->persist($response);
                $em->flush();

                $note = count($response->getQuestionMark());
            }
            echo $request->request->get('value') . ',' . $note;
        }
        exit;
    }

    //function for repositioning Element Position
    public function questionnaireResponseTotalNoteAjaxSaveAction($slug_ent, $slug_quest, $repondant_id) {
        $request = $this->get('request');
        $em = $this->getDoctrine()->getManager();
        $questionnaire_user = $this->get('questionnaire_user_repository')->getElements(array('by_id' => $repondant_id, 'action' => 'one')); 
        if ($request->request->get('value')) {
            $score = 0;
            $nbNote = 0;
            foreach($questionnaire_user->getScore() as $k => $v){
                if(is_int($k)){
                    $score += $v;
                    $nbNote++;
                }
            }
            if($nbNote == 0)
                $nbNote = 1;
            $scoreMoyen = round($score / $nbNote, 2);
            if ($questionnaire_user->getScore()) {
                $mergedArray = array_merge(array($this->getUser()->getId() => $request->request->get('value'), 'average' => $scoreMoyen), $questionnaire_user->getScore());
            } else {
                $mergedArray = array($this->getUser()->getId() => $request->request->get('value'), 'average' => $scoreMoyen);
            }
            if (trim($request->request->get('comments'))) {
                $commentArray = array($this->getUser()->getId() => trim($request->request->get('comments')))+(is_array($questionnaire_user->getComments())?$questionnaire_user->getComments():array());
                $questionnaire_user->setComments($commentArray);
            }
            $questionnaire_user->setScore($mergedArray);
            $em->persist($questionnaire_user);
            $em->flush();
            
            $this->updateQuestionnaireUserRank($slug_quest);
            echo $request->request->get('value');
        }
        exit;
    }
    
    private function updateQuestionnaireUserRank($slug_quest){
        $em = $this->getDoctrine()->getManager();
        $questionnaire = $this->get('questionnaire_repository')->getElements(array('by_slug'=>$slug_quest,'action' => 'one'));
        $questionnaireUserRank = array();
        if($questionnaire){
            $userId = $this->getUser()->getId();
            foreach($questionnaire->getQuestionnaireUser() as $questionnaireUserObj){
                if(is_array($questionnaireUserObj->getScore())){
                    $questionnaireUserRank[$questionnaireUserObj->getId()] = 0;
                    foreach($questionnaireUserObj->getScore() as $score){
                        $questionnaireUserRank[$questionnaireUserObj->getId()] += floatval(explode('/',$score)[0]);
                    }
                    
                }
                
            }
            
            if(count(array_unique($questionnaireUserRank))!=1){
                asort($questionnaireUserRank);
            }
                
            foreach(array_keys($questionnaireUserRank) as $key => $id){
                $questionnaireUserObj = $this->get('questionnaire_user_repository')->getElements(array('by_id' => $id, 'action' => 'one'));
                $questionnaireUserObj->setRank($key+1);
                $em->persist($questionnaireUserObj);
                $em->flush();
            }
        }
       //echo "<pre>";print_r($questionnaireUserRank);print_r(array_keys($questionnaireUserRank));die;
    }
    
    
    public function removeQuestionnaireUserAction($repondant_id){
        $repondant = $this->get('questionnaire_user_repository')->getElements(array('by_id'=>$repondant_id,'action'=>'one'));
        if($repondant){
            $em = $this->getDoctrine()->getManager();
            foreach($repondant->getQuestionnaireQuestionResponse() as $response){
                $em->remove($response);
            }               
            $questionnaire = $repondant->getQuestionnaire();
            $em->remove($repondant);
            $em->flush();
            $this->updateQuestionnaireUserRank($questionnaire->getSlug());
            return $this->redirect($this->getRequest()->headers->get('referer'));
        }else{
            echo 'Invalid argument passed.';exit;
        }
    }

    //function for repositioning Element Position
    public function questionnaireElementRepositionAction() {
        $request = $this->get('request');

        $list = $request->get('list');
        $parent = $list['parent'];

        //if list order is changed
        if (isset($list['order']) && count($list['order']) > 0) {
            foreach ($list['order'] as $key => $lst) {
                $exploded_list = explode('_', $lst);
                if (count($exploded_list) == 2) {
                    $id = $exploded_list[1];
                }
                if (count($exploded_list) == 3) {
                    $id = $exploded_list[2];
                }
                if (count($exploded_list) == 4) {
                    $id = $exploded_list[3];
                }
                
                $elemObj = $this->get('questionnaire_element_repository')->getElements(array('by_id' => $id, 'action' => 'one'));
                $em = $this->getDoctrine()->getManager();

                //if no change in level 
                if ($elemObj->getParent()) {
                    if ((($elemObj->getParent()->getId()) == $parent) && ($elemObj->getLevel() == $list['level'])) {

                        //setting enterprise
                        $elemObj->setPosition($key + 1);
                        $em->persist($elemObj);
                        $em->flush();
                    } else {

                        if ($parent != 0) {
                            $parentInfo = $this->get('questionnaire_element_repository')->getElements(array('by_id' => $parent, 'action' => 'one'));
                            //setting parent
                            $elemObj->setParent($parentInfo);
                        } else {
                            $elemObj->setParent(NULL);
                        }
                        //setting enterprise
                        $elemObj->setPosition($key + 1);
                        $em->persist($elemObj);
                        $em->flush();
                    }
                } else {

                    if ($parent != 0) {
                        $parentInfo = $this->get('questionnaire_element_repository')->getElements(array('by_id' => $parent, 'action' => 'one'));
                        //setting parent
                        $elemObj->setParent($parentInfo);
                    } else {
                        $elemObj->setParent(NULL);
                    }
                    //setting enterprise
                    $elemObj->setPosition($key + 1);
                    $em->persist($elemObj);
                    $em->flush();
                }
                
                //If element level is greater than 0 then time limit NULL
                if($elemObj->getLevel()>0){
                    $elemObj->setTimeLimit(NULL);
                    $em->persist($elemObj);
                    $em->flush();
                }
            }
        }

        exit;
    }

    public function videoAction() {
        $request = $this->get('request');
        $slug_quest = $request->get('slug_quest');
        //Questionnaire information
        $questionnaire = $this->get('questionnaire_repository')->getElements(array('by_slug' => $slug_quest, 'action' => 'one'));
        //elements list
        $elements = $this->get('questionnaire_element_repository')->getElements(array('by_questionnaire_id' => $questionnaire->getId()));
        return $this->render('VMQuestionnaireBundle:Form:Element/Question/webcam_form.html.twig', array('questionnaire' => $questionnaire, 'elements' => $elements));
    }

    //To delete questionnaire element 
    public function questionnaireElementRemoveAction() {
        $request = $this->get('request');
        $id = $request->get('id');

        //getting questionnaire information by slug
        $questionnaire = $this->get('questionnaire_repository')->getElements(array('by_slug' => $request->get('slug_quest'), 'action' => 'one'));

        //getting information of element by id for whcih delete operation will performed
        $element = $this->get('questionnaire_element_repository')->getElements(array('by_id' => $id, 'action' => 'one'));

        $elem_type = $element->getStdQuestionnaireTypeElement()->getSlug();

        $parent_id = $element->getParent() ? $element->getParent()->getId() : NULL;

        //getting Others sibling elements of element which will be deleted for reposition
        $otherElements = $this->get('questionnaire_element_repository')->getElements(array('by_questionnaire_id' => $questionnaire->getId(), 'by_level' => $element->getLevel(), 'parent_id' => $parent_id, 'by_not_id' => $element->getId(), 'order_by' => array('field' => 'position', 'sort' => 'asc')));
        $em = $this->getDoctrine()->getManager();
        $root_path = $this->getRequest()->server->get('DOCUMENT_ROOT');
        
        if ($element) {
            //If group have chiildern  
            if (count($element->getChildren()) > 0) {
                foreach ($element->getChildren() as $objChild) {

                    //if group has subchild of child 
                    if (count($objChild->getChildren()) > 0) {

                        foreach ($objChild->getChildren() as $objSubChild) {
                            //if question type element
                            if ($objSubChild->getStdQuestionnaireTypeElement()->getSlug() == 'question') {
                                $subchild_question = $this->get('question_repository')->getElements(array('by_id' => $objSubChild->getQuestion()->getId(), 'action' => 'one'));

                                // Remove choices of question type element if have choices type question
                                if (count($subchild_question->getQuestionnaireQuestionChoice()) > 0) {
                                    foreach ($subchild_question->getQuestionnaireQuestionChoice() as $objSubChoice) {
                                        $em->remove($objSubChoice);
                                        $em->flush();
                                    }
                                }


                                //remove question
                                $em->remove($subchild_question);
                                $em->flush();
                            }
                            //remove sub subelement   
                            if($objSubChild->getEnclosedFiles()!='' && $objSubChild->getMediaType()!='embed'){
                                if(file_exists($root_path.$objSubChild->getEnclosedFiles())){
                                   unlink($root_path.$objSubChild->getEnclosedFiles());
                                }   
                            }
                            
                            $em->remove($objSubChild);
                            $em->flush();
                        }
                    }

                    if ($objChild->getStdQuestionnaireTypeElement()->getSlug() == 'question') {
                        $subchild_question = $this->get('question_repository')->getElements(array('by_id' => $objChild->getQuestion()->getId(), 'action' => 'one'));

                        // Remove choices of question type element if have choices type question
                        if (count($subchild_question->getQuestionnaireQuestionChoice()) > 0) {
                            foreach ($subchild_question->getQuestionnaireQuestionChoice() as $objChoice) {
                                $em->remove($objChoice);
                                $em->flush();
                            }
                        }

                        //remove question   
                        $em->remove($subchild_question);
                        $em->flush();
                    }

                    //remove sub subelement   
                    if($objChild->getEnclosedFiles()!='' && $objChild->getMediaType()!='embed'){
                        if(file_exists($root_path.$objChild->getEnclosedFiles())){
                           unlink($root_path.$objChild->getEnclosedFiles());
                        }   
                    }
                    //remove sub element     
                    $em->remove($objChild);
                    $em->flush();
                }
            }

            //setting position in new order
            if (count($otherElements) > 0) {
                $key = 0;
                foreach ($otherElements as $elm) {
                    //setting position
                    $elm->setPosition($key + 1);
                    $em->persist($elm);
                    $em->flush();
                    $key++;
                }
            }

            //If deleting Question 
            if ($elem_type == 'question') {
                $question = $this->get('question_repository')->getElements(array('by_id' => $element->getQuestion()->getId(), 'action' => 'one'));

                // Remove choices of question type element if have choices type question
                if (count($question->getQuestionnaireQuestionChoice()) > 0) {
                    foreach ($question->getQuestionnaireQuestionChoice() as $obj) {
                        $em->remove($obj);
                        $em->flush();
                    }
                }

                //delete question
                $em->remove($question);
                $em->flush();
            }

            //remove sub subelement   
            if($element->getEnclosedFiles()!='' && $element->getMediaType()!='embed'){
                if(file_exists($root_path.$element->getEnclosedFiles())){
                  unlink($root_path.$element->getEnclosedFiles());
                }
            }
                    
            //delete element
            $em->remove($element);
            $em->flush();
        }

        return $this->redirect($this->getRequest()->headers->get('referer'));
    }

}

<?php

namespace VM\QuestionnaireBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use VM\QuestionnaireBundle\Entity\Help;
use VM\QuestionnaireBundle\Form\HelpType;

class HelpController extends Controller {

    public function boIndexAction() {
        $paginate = $this->get("index_paginate");
        $paginate->setH1('Tous les help');
        $paginate->setView('VMQuestionnaireBundle:Back:Help/index.html.twig');
        $paginate->setAddNew('bo_help_new');

        $query = $this->get('help_repository')->getElements($paginate->getParamsForQuery());

        $paginate->setQuery($query);
        return $this->render($paginate->getTemplate(), $paginate->getParams());
    }

    public function boShowAction($id) {
        $help = $this->get('help_repository')->getElements(array('by_id' => $id, 'action' => 'one'));

        if($help){
            $breadcrumbs = $this->get("white_october_breadcrumbs");
            $breadcrumbs->addItem("Tous les help", $this->get("router")->generate("bo_helps"));
            $breadcrumbs->addItem('');
            return $this->render('VMQuestionnaireBundle:Back:Help/show.html.twig', array( 'help' => $help));
        }else{
            return $this->redirect($this->generateUrl('bo_helps'));
        }
    }
    
    public function helpFormAction(){
        $request= $this->getRequest();
        $formConf = $this->get('form_model');
        $formConf->setView('VMQuestionnaireBundle:Back:Help/help_form.html.twig');
        $formConf->setElement('help');
        

        // REDIRECTION PART
        if(in_array($formConf->getAction(), array('edit', 'update'))){
            if($this->get('request')->get('id')){
                $object = $this->get('help_repository')->getElements(array('by_id' => $this->get('request')->get('id'), 'action' => 'one'));
                if($object){
                    $formConf->setUrlParams(array('id' => $object->getId()));
                    $formConf->setH1('Modifier le help '.$object->getText());
                }else{

                }
            }else{

            }
        }else{
            $object = new Help();
            $formConf->setH1('Ajouter un help');
        }
 
        $formConf->setForm(new HelpType($this->getDoctrine()->getEntityManager()), $object,array('dataObj'=>$object));

        if($this->get('request')->getMethod() == 'POST'){
            // Make Validation Part
            $params = array();
            $params = $this->helpProcessForm($formConf->getForm(), $formConf->getObject(), $params);

            // See if is success or not and redirect to the show page or return page with error
            if (isset($params['url_success'])) {
                if ($this->get('request')->isXmlHttpRequest()) { return new Response($params['url_success']); }
                else { return $this->redirect($params['url_success']); }
            }
        }

        return $this->render($formConf->getTemplate(), $formConf->getParams());
       
    }
    
    //function to perform adding and updating a help
    private function helpProcessForm($form, $obj, $params) {       
        $helpForm= $this->getRequest()->get($form->getName());
        if ($form->isValid()) {
            
            $em = $this->getDoctrine()->getManager();
            if(array_key_exists('text', $helpForm) && $helpForm['text']!=''){
                $helpObj=$em->getRepository('VMQuestionnaireBundle:Help')->findOneByText($helpForm['text']);
                if($helpObj){
                    $obj=$helpObj;
                }                    
            }
            $em->persist($obj);
            $em->flush();
            if(array_key_exists('std_question_type', $helpForm) && $helpForm['std_question_type']!=''){
                $typeQues=$em->getRepository('VMStandardBundle:StdQuestionType')->find($helpForm['std_question_type']);
                $typeQues->setHelp($obj);
                $em->persist($typeQues);
                $em->flush();
            }
            if(array_key_exists('std_questionnaire_type', $helpForm) && $helpForm['std_questionnaire_type']!=''){
                $typeQuestionaires=$em->getRepository('VMStandardBundle:StdQuestionnaireType')->find($helpForm['std_questionnaire_type']);
                $typeQuestionaires->setHelp($obj);
                $em->persist($typeQuestionaires);
                $em->flush();
            }

            $params['url_success'] = $this->generateUrl('bo_help_show', array('id' => $obj->getId()));
        } else {
            $params['errors'] = $form->getErrors();
        }

        return $params;
    }
    
    public function boDeleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $help = $em->getRepository('VMQuestionnaireBundle:Help')->find($id);
        if(is_object($help) && $help->getId()){
            $em->remove($help);
            $em->flush(); 
            return $this->redirect($this->generateUrl('bo_helps'));   
            exit;
        }
        
    }

}

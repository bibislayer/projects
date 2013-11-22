<?php

namespace FP\StandardBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FP\StandardBundle\Entity\StdQuestionnaireType;
use FP\StandardBundle\Form\StdQuestionnaireTypeType;

class TypeQuestionnaireController extends Controller
{
    public function boIndexAction() {
        $paginate = $this->get("index_paginate");
        $paginate->setH1('Tous les type questionnaires');
        $paginate->setView('FPStandardBundle:TypeQuestionnaire:index.html.twig');
        $paginate->setAddNew('bo_type_questionnaire_new');

        $query = $this->get('std_questionnaire_type_repository')->getElements($paginate->getParamsForQuery());

        $paginate->setQuery($query);
        return $this->render($paginate->getTemplate(), $paginate->getParams());
    }

    public function boShowAction($id) {
        $typeQuestionnaire = $this->get('std_questionnaire_type_repository')->getElements(array('by_id' => $id, 'action' => 'one'));

        if($typeQuestionnaire){
            $breadcrumbs = $this->get("white_october_breadcrumbs");
            $breadcrumbs->addItem("Tous les type questionnaire", $this->get("router")->generate("bo_type_questionnaires"));
            $breadcrumbs->addItem('');
            return $this->render('FPStandardBundle:TypeQuestionnaire:show.html.twig', array( 'typeQuestionnaire' => $typeQuestionnaire));
        }else{
            return $this->redirect($this->generateUrl('bo_type_questionnaires'));
        }
    }
    
    public function typeQuestionnaireFormAction(){
        $request= $this->getRequest();
        $formConf = $this->get('form_model');
        $formConf->setView('FPStandardBundle:TypeQuestionnaire:type_questionnaire_form.html.twig');
        $formConf->setElement('type_questionnaire');

        // REDIRECTION PART
        if(in_array($formConf->getAction(), array('edit', 'update'))){
            if($this->get('request')->get('id')){
                $object = $this->get('std_questionnaire_type_repository')->getElements(array('by_id' => $this->get('request')->get('id'), 'action' => 'one'));
                if($object){
                    $formConf->setUrlParams(array('id' => $object->getId()));
                    $formConf->setH1('Modifier le type questionnaire '.$object->getName());
                }else{

                }
            }else{

            }
        }else{
            $object = new StdQuestionnaireType();
            $formConf->setH1('Ajouter un type questionnaire');
        }
 
        $formConf->setForm(new StdQuestionnaireTypeType($this->getDoctrine()), $object);

        if($this->get('request')->getMethod() == 'POST'){
            // Make Validation Part
            $params = array();
            $params = $this->typeQuestionnaireProcessForm($formConf->getForm(), $formConf->getObject(), $params);

            // See if is success or not and redirect to the show page or return page with error
            if (isset($params['url_success'])) {
                if ($this->get('request')->isXmlHttpRequest()) { return new Response($params['url_success']); }
                else { return $this->redirect($params['url_success']); }
            }
        }

        return $this->render($formConf->getTemplate(), $formConf->getParams());
       
    }
    
    //function to perform adding and updating a category
    private function typeQuestionnaireProcessForm($form, $obj, $params) {
        
        $categorie = $this->getRequest()->get($form->getName());
        
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($obj);
            $em->flush();

            $params['url_success'] = $this->generateUrl('bo_type_questionnaire_show', array('id' => $obj->getId()));
        } else {
            $params['errors'] = $form->getErrors();
        }

        return $params;
    }
    
    public function boDeleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $typeQuestionnaire = $em->getRepository('FPStandardBundle:StdQuestionnaireType')->find($id);
        if(is_object($typeQuestionnaire) && $typeQuestionnaire->getId()){
            $em->remove($typeQuestionnaire);
            $em->flush(); // clear cached nodes
            return $this->redirect($this->generateUrl('bo_type_questionnaires'));   
            exit;
        }
        
    }
}

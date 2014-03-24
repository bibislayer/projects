<?php

namespace Poker\StandardBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Poker\StandardBundle\Entity\StdQuestionnaireTypeElement;
use Poker\StandardBundle\Form\StdQuestionnaireTypeElementType;

class TypeQuestionnaireElementController extends Controller
{
    public function boIndexAction() {
        $paginate = $this->get("index_paginate");
        $paginate->setH1('Tous les type questionnaire elements');
        $paginate->setView('PokerStandardBundle:TypeQuestionnaireElement:index.html.twig');
        $paginate->setAddNew('bo_type_questionnaire_element_new');

        $query = $this->get('std_questionnaire_type_element_repository')->getElements($paginate->getParamsForQuery());

        $paginate->setQuery($query);
        return $this->render($paginate->getTemplate(), $paginate->getParams());
    }

    public function boShowAction($id) {
        $typeQuestionnaireElement = $this->get('std_questionnaire_type_element_repository')->getElements(array('by_id' => $id, 'action' => 'one'));

        if($typeQuestionnaireElement){
            $breadcrumbs = $this->get("white_october_breadcrumbs");
            $breadcrumbs->addItem("Tous les type questionnaire element", $this->get("router")->generate("bo_type_questionnaire_elements"));
            $breadcrumbs->addItem('');
            return $this->render('PokerStandardBundle:TypeQuestionnaireElement:show.html.twig', array( 'typeQuestionnaireElement' => $typeQuestionnaireElement));
        }else{
            return $this->redirect($this->generateUrl('bo_type_questionnaire_elements'));
        }
    }
    
    public function typeQuestionnaireElementFormAction(){
        $request= $this->getRequest();
        $formConf = $this->get('form_model');
        $formConf->setView('PokerStandardBundle:TypeQuestionnaireElement:type_questionnaire_element_form.html.twig');
        $formConf->setElement('type_questionnaire_element');

        // REDIRECTION PART
        if(in_array($formConf->getAction(), array('edit', 'update'))){
            if($this->get('request')->get('id')){
                $object = $this->get('std_questionnaire_type_element_repository')->getElements(array('by_id' => $this->get('request')->get('id'), 'action' => 'one'));
                if($object){
                    $formConf->setUrlParams(array('id' => $object->getId()));
                    $formConf->setH1('Modifier le type questionnaire '.$object->getName());
                }else{

                }
            }else{

            }
        }else{
            $object = new StdQuestionnaireTypeElement();
            $formConf->setH1('Ajouter un type questionnaire element');
        }
 
        $formConf->setForm(new StdQuestionnaireTypeElementType($this->getDoctrine()), $object);

        if($this->get('request')->getMethod() == 'POST'){
            // Make Validation Part
            $params = array();
            $params = $this->typeQuestionnaireElementProcessForm($formConf->getForm(), $formConf->getObject(), $params);

            // See if is success or not and redirect to the show page or return page with error
            if (isset($params['url_success'])) {
                if ($this->get('request')->isXmlHttpRequest()) { return new Response($params['url_success']); }
                else { return $this->redirect($params['url_success']); }
            }
        }

        return $this->render($formConf->getTemplate(), $formConf->getParams());
       
    }
    
    //function to perform adding and updating a category
    private function typeQuestionnaireElementProcessForm($form, $obj, $params) {
        
        $categorie = $this->getRequest()->get($form->getName());
        
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($obj);
            $em->flush();

            $params['url_success'] = $this->generateUrl('bo_type_questionnaire_element_show', array('id' => $obj->getId()));
        } else {
            $params['errors'] = $form->getErrors();
        }

        return $params;
    }
    
    public function boDeleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $typeQuestionnaire = $em->getRepository('PokerStandardBundle:StdQuestionnaireTypeElement')->find($id);
        if(is_object($typeQuestionnaire) && $typeQuestionnaire->getId()){
            $em->remove($typeQuestionnaire);
            $em->flush(); // clear cached nodes
            return $this->redirect($this->generateUrl('bo_type_questionnaire_elements'));   
            exit;
        }
        
    }
}

<?php

namespace FP\StandardBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FP\StandardBundle\Entity\StdQuestionType;
use FP\StandardBundle\Form\StdQuestionTypeType;

class TypeQuestionController extends Controller
{
    public function boIndexAction() {
        $paginate = $this->get("index_paginate");
        $paginate->setH1('Tous les type questions');
        $paginate->setView('FPStandardBundle:TypeQuestion:index.html.twig');
        $paginate->setAddNew('bo_type_question_new');

        $query = $this->get('std_question_type_repository')->getElements($paginate->getParamsForQuery());

        $paginate->setQuery($query);
        return $this->render($paginate->getTemplate(), $paginate->getParams());
    }

    public function boShowAction($id) {
        $typeQuestion = $this->get('std_question_type_repository')->getElements(array('by_id' => $id, 'action' => 'one'));

        if($typeQuestion){
            $breadcrumbs = $this->get("white_october_breadcrumbs");
            $breadcrumbs->addItem("Tous les type question", $this->get("router")->generate("bo_type_questions"));
            $breadcrumbs->addItem('');
            return $this->render('FPStandardBundle:TypeQuestion:show.html.twig', array( 'typeQuestion' => $typeQuestion));
        }else{
            return $this->redirect($this->generateUrl('bo_type_questions'));
        }
    }
    
    public function typeQuestionFormAction(){
        $request= $this->getRequest();
        $formConf = $this->get('form_model');
        $formConf->setView('FPStandardBundle:TypeQuestion:type_question_form.html.twig');
        $formConf->setElement('type_question');

        // REDIRECTION PART
        if(in_array($formConf->getAction(), array('edit', 'update'))){
            if($this->get('request')->get('id')){
                $object = $this->get('std_question_type_repository')->getElements(array('by_id' => $this->get('request')->get('id'), 'action' => 'one'));
                if($object){
                    $formConf->setUrlParams(array('id' => $object->getId()));
                    $formConf->setH1('Modifier le type question '.$object->getName());
                }else{

                }
            }else{

            }
        }else{
            $object = new StdQuestionType();
            $formConf->setH1('Ajouter un type question');
        }
 
        $formConf->setForm(new StdQuestionTypeType($this->getDoctrine()), $object);

        if($this->get('request')->getMethod() == 'POST'){
            // Make Validation Part
            $params = array();
            $params = $this->typeQuestionProcessForm($formConf->getForm(), $formConf->getObject(), $params);

            // See if is success or not and redirect to the show page or return page with error
            if (isset($params['url_success'])) {
                if ($this->get('request')->isXmlHttpRequest()) { return new Response($params['url_success']); }
                else { return $this->redirect($params['url_success']); }
            }
        }

        return $this->render($formConf->getTemplate(), $formConf->getParams());
       
    }
    
    //function to perform adding and updating a category
    private function typeQuestionProcessForm($form, $obj, $params) {
        
        $categorie = $this->getRequest()->get($form->getName());
        
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($obj);
            $em->flush();

            $params['url_success'] = $this->generateUrl('bo_type_question_show', array('id' => $obj->getId()));
        } else {
            $params['errors'] = $form->getErrors();
        }

        return $params;
    }
    
    public function boDeleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $typeQuestion = $em->getRepository('FPStandardBundle:StdQuestionType')->find($id);
        if(is_object($typeQuestion) && $typeQuestion->getId()){
            $em->remove($typeQuestion);
            $em->flush(); // clear cached nodes
            return $this->redirect($this->generateUrl('bo_type_questions'));   
            exit;
        }
        
    }
}

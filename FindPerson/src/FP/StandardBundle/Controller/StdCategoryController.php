<?php

namespace FP\StandardBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use FP\StandardBundle\Entity\StdCategory;
use FP\StandardBundle\Form\StdCategoryType;

class StdCategoryController extends Controller {

    public function boIndexAction() {
        $paginate = $this->get("index_paginate");
        $paginate->setH1('Tous les category');
        $paginate->setView('FPStandardBundle:StdCategory/Back:index.html.twig');
        $paginate->setAddNew('bo_category_new');

        $query = $this->get('std_category_repository')->getCategory($paginate->getParamsForQuery());

        $paginate->setQuery($query);
        return $this->render($paginate->getTemplate(), $paginate->getParams());
    }

    public function boShowAction($id) {
        $category = $this->get('std_category_repository')->getCategory(array('by_id' => $id, 'action' => 'one'));

        if($category){
            $breadcrumbs = $this->get("white_october_breadcrumbs");
            $breadcrumbs->addItem("Tous les category", $this->get("router")->generate("bo_categories"));
            $breadcrumbs->addItem('');
            return $this->render('FPStandardBundle:StdCategory/Back:show.html.twig', array( 'category' => $category));
        }else{
            return $this->redirect($this->generateUrl('bo_categories'));
        }
    }
    
    public function categoryFormAction(){
        $request= $this->getRequest();
        $formConf = $this->get('form_model');
        $formConf->setView('FPStandardBundle:StdCategory/Back:category_form.html.twig');
        if($request->get('cat_id')){
            $formConf->setElement('category_child');
            $formConf->setUrlParams(array('cat_id'=>$request->get('cat_id')));
        }else{
            $formConf->setElement('category');
        }
        

        // REDIRECTION PART
        if(in_array($formConf->getAction(), array('edit', 'update'))){
            if($this->get('request')->get('id')){
                $object = $this->get('std_category_repository')->getCategory(array('by_id' => $this->get('request')->get('id'), 'action' => 'one'));
                if($object){
                    $formConf->setUrlParams(array('id' => $object->getId()));
                    $formConf->setH1('Modifier le category '.$object->getName());
                }else{

                }
            }else{

            }
        }else{
            $object = new StdCategory();
            $formConf->setH1('Ajouter un category');
        }
 
        $formConf->setForm(new StdCategoryType($this->getDoctrine()), $object);

        if($this->get('request')->getMethod() == 'POST'){
            // Make Validation Part
            $params = array();
            $params = $this->categoryProcessForm($formConf->getForm(), $formConf->getObject(), $params);

            // See if is success or not and redirect to the show page or return page with error
            if (isset($params['url_success'])) {
                if ($this->get('request')->isXmlHttpRequest()) { return new Response($params['url_success']); }
                else { return $this->redirect($params['url_success']); }
            }
        }

        return $this->render($formConf->getTemplate(), $formConf->getParams());
       
    }
    
    //function to perform adding and updating a category
    private function categoryProcessForm($form, $obj, $params) {
        
        $categorie = $this->getRequest()->get($form->getName());
        
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            if(array_key_exists('parent_id', $categorie) && $categorie['parent_id']!=''){
                $category = $this->get('std_category_repository')->getCategory(array('by_id' => $categorie['parent_id'], 'action' => 'one'));
                $obj->setParent($category);
            }
            $em->persist($obj);
            $em->flush();

            $params['url_success'] = $this->generateUrl('bo_category_show', array('id' => $obj->getId()));
        } else {
            $params['errors'] = $form->getErrors();
        }

        return $params;
    }
    

    public function boDeleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $category = $em->getRepository('FPStandardBundle:StdCategory')->find($id);
        if(is_object($category) && $category->getId()){
            $repo = $em->getRepository('FPStandardBundle:StdCategory');
            $repo->removeFromTree($category);
            $em->clear(); // clear cached nodes
            return $this->redirect($this->generateUrl('bo_categories'));   
            exit;
        }
        
    }
    public function changeStatusAction($id, $type){
        $em = $this->getDoctrine()->getManager();
        $category = $em->getRepository('FPStandardBundle:StdCategory')->find($id);
        if(is_object($category) && $category->getId()){
            if($type=='publish'){
                $category->setPublished(1);
                $category->setApprobation(1);
            }else if($type=='unpublish'){
                $category->setPublished(0);
                $category->setApprobation(0);
            }
            $em->persist($category);
            $em->flush();
            return $this->redirect($this->getRequest()->headers->get('referer')); 
            exit;
        }  else {
            return $this->redirect($this->getRequest()->headers->get('referer').'?error=Invalid Argument');   
            exit;
        }  
    }

}

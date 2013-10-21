<?php

namespace SO\StandardBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use SO\StandardBundle\Entity\StdDiplomaType;
use SO\StandardBundle\Form\StdDiplomaTypeType;
use SO\StandardBundle\Filter\StdDiplomaTypeFilterType;


class StdDiplomaTypeController extends Controller {

    public function indexAction() {
     
        $form = $this->get('form.factory')->create(new StdDiplomaTypeFilterType());
        $form->bind($this->get('request'));
        $conf = array('display' => array('by_status'));
        $p = $this->get('standard_controller')->initializeFilterAndPagination($this->get('request')->query->all(), $conf); 
        
        $diplomaTypes = $this->get('std_diploma_type_repository')->getStdDiplomaTypes($p['params']);
        
        $params = array(
            'elements' => $diplomaTypes,
            'params' => $p['params'],
            'filters' => array('fields' => $p['filters'], 'form' => $form->createView()) ,
            'h1' => 'Diploma type management',
            'view' => 'SOStandardBundle:StdDiplomaType:_boIndex.html.twig',
            'addUrl' => 'bo_diploma_type_new'
        );

        if($this->get('request')->isXmlHttpRequest()){
            return $this->render('SOStandardBundle:Backend:list.html.twig', $params);
        }else{
            return $this->render('SOStandardBundle:Backend:index.html.twig', $params);
        }
    }
    
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager();
        $diplomaType = $em->getRepository('SOStandardBundle:StdDiplomaType')->findOneById($id);    
        return $this->render('SOStandardBundle:StdDiplomaType:show.html.twig', array( 'diplomaType' => $diplomaType));           
    }
    
     public function newAction() {
         
         // Get base
        $request = $this->get('request');
        $otherParams = array();
        
        $obj = new StdDiplomaType();
        $form = $this->createForm(new StdDiplomaTypeType(), $obj);
        
        // Get the configuration for the form
        $params = $this->configureFormParams($request, array('form' => $form), $otherParams);
        if (isset($params['redirect'])) { return $this->redirect($params['redirect']); }
       
        // Display page
        return $this->render($params['page'], array('params' => $params));
    }

    
    public function createAction() {
        
        // Get base
        $request = $this->get('request');
        $otherParams = array();
        
        $obj = new StdDiplomaType();
        $form = $this->createForm(new StdDiplomaTypeType(), $obj);
        $form->handleRequest($request);
        
        // Get the configuration for the form
        $params = $this->configureFormParams($request, array('form' => $form), $otherParams);
        if (isset($params['redirect'])) { return $this->redirect($params['redirect']); }

        // Make Validation Part
        $params = $this->processForm($form, $obj, $params);
        
        // See if is success or not and redirect to the show page or return page with error
        if (isset($params['url_success'])) {
            if ($request->isXmlHttpRequest()) { return new Response($params['url_success']); }
            else { return $this->redirect($params['url_success']); }
        } else {
            return $this->render($params['page'], array('params' => $params));
        }
    }

    public function editAction($id) {
        
        $request = $this->get('request');
        $otherParams = array();
        
        $otherParams['entity'] = $this->get('std_diploma_type_repository')->getStdDiplomaTypes(array('by_id' => $id, 'action' => 'one'));
        $form = $this->createForm(new StdDiplomaTypeType(), $otherParams['entity']);

        $params = $this->configureFormParams($request, array('form' => $form), $otherParams);
        if (isset($params['redirect'])) { return $this->redirect($params['redirect']); }

        return $this->render($params['page'], array('params' => $params));              
    }

    public function updateAction($id) {
        
        $request = $this->get('request');
        $otherParams = array();

        $otherParams['entity'] = $this->get('std_diploma_type_repository')->getStdDiplomaTypes(array('by_id' => $id, 'action' => 'one'));

        $form = $this->createForm(new StdDiplomaTypeType(), $otherParams['entity']);
        $form->handleRequest($request);


        $params = $this->configureFormParams($request, array('form' => $form), $otherParams);
        if (isset($params['redirect'])) {
            return $this->redirect($params['redirect']);
        }

        $params = $this->processForm($form, $otherParams['entity'], $params);

        if (isset($params['url_success'])) {
            if ($request->isXmlHttpRequest()) { return new Response($params['url_success']); }
            else { return $this->redirect($params['url_success']); }
        } else {
            return $this->render($params['page'], array('params' => $params));
        }
    }

    //function to perform adding and updating a school
    public function processForm($form, $diplomaTypeObj, $params) {        
        
        if ($form->isValid()) {                  
          
            $em = $this->getDoctrine()->getManager();
            $em->persist($diplomaTypeObj);
            $em->flush();
            
            if ($params['env'] == 'bo_') {
                $params['url_success'] = $this->generateUrl('bo_diploma_type_show', array('id' => $diplomaTypeObj->getId()));
            } elseif ($params['env'] == 'mo_') {
                $params['url_success'] = $this->generateUrl('mo_diploma_type_show', array('id' => $diplomaTypeObj->getId()));
            }
        } else {
            $params['errors'] = $form->getErrors();
        }

        return $params;
    }
    
    /****
     * Remove record permanently from data 
     */
    public function removeAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $diplomaType = $em->getRepository('SOStandardBundle:StdDiplomaType')->find($id);        
        if(is_object($diplomaType) && $diplomaType->getId()){
            $em->remove($diplomaType);
            $em->flush(); // clear cached nodes
            return $this->redirect($this->generateUrl('bo_diploma_type_index'));  
        }else{
            return $this->redirect($this->generateUrl('bo_diploma_type_index',array('error'=>'Invalid Argument')));    
        }        
    }
    /****
     * Change status of diploma type
     */    
    public function changeStatusAction($id, $type){
        $em = $this->getDoctrine()->getManager();
        $diplomaType = $em->getRepository('SOStandardBundle:StdDiplomaType')->find($id);
        if(is_object($diplomaType) && $diplomaType->getId()){
            if($type=='publish'){
                $diplomaType->setPublished(1);
                $diplomaType->setApprobation(1);
            }else if($type=='unpublish'){
                $diplomaType->setPublished(0);
                $diplomaType->setApprobation(0);
            }
            $em->persist($diplomaType);
            $em->flush();
            return $this->redirect($this->getRequest()->headers->get('referer'));             
        }  else {
            return $this->redirect($this->getRequest()->headers->get('referer').'?error=Invalid argument');   
        }  
    }
    
    
    
    /***
     * Generic function for form that would be render commonly
     */
    private function configureFormParams($request, $par = array(), $objects = array())
    {
        // Initialize Array, form view and get environment
        $params = array();
        $params['route'] = array('element' => 'diploma_type', 'params' => array());
        $params['env'] = substr($request->attributes->get('_route'), 0, 3);
        $params['view'] = 'SOStandardBundle:StdDiplomaType:form.html.twig';
        $params['form'] = $par['form']->createView();
       
        // Construct h1, route
        if (isset($objects['entity'])) {
            $params['route']['action'] = 'update';
            $params['h1'] = 'Modifier diplome type';
            $params['entity'] = $objects['entity'];
            $params['route']['params']['id'] = $objects['entity']->getId();
        } else {
            $params['route']['action'] = 'create';  
            $params['h1'] = 'Ajouter une diplome type';
        }

        // Get the good template
        $params = $this->get('standard_controller')->getGenericFormTemplate($request, $params);

        $params['url'] = $this->generateUrl($params['route']['name'], $params['route']['params']);

        return $params;
    }
}

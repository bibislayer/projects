<?php

namespace SO\StandardBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use SO\StandardBundle\Entity\StdSchoolStatus;
use SO\StandardBundle\Form\StdSchoolStatusType;
use SO\StandardBundle\Filter\StdSchoolStatusFilterType;


class StdSchoolStatusController extends Controller {

    public function indexAction() {
     
        $form = $this->get('form.factory')->create(new StdSchoolStatusFilterType());
        $form->bind($this->get('request'));
        $conf = array();
        $p = $this->get('standard_controller')->initializeFilterAndPagination($this->get('request')->query->all(), $conf);        
        $schoolStatus = $this->get('std_school_status_repository')->getStdSchoolStatus($p['params']);
        
        $params = array(
            'elements' => $schoolStatus,
            'params' => $p['params'],
            'filters' => array('fields' => $p['filters'], 'form' => $form->createView()) ,
            'h1' => 'School status management',
            'view' => 'SOStandardBundle:StdSchoolStatus:_boIndex.html.twig',
            'addUrl' => 'bo_school_status_new'
        );

        if($this->get('request')->isXmlHttpRequest()){
            return $this->render('SOStandardBundle:Backend:list.html.twig', $params);
        }else{
            return $this->render('SOStandardBundle:Backend:index.html.twig', $params);
        }
    }
    
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager();
        $schoolStatus = $em->getRepository('SOStandardBundle:StdSchoolStatus')->findOneById($id);
        return $this->render('SOStandardBundle:StdSchoolStatus:show.html.twig', array( 'schoolStatus' => $schoolStatus));           
    }
    
    public function newAction() {
        
        // Get base
        $request = $this->get('request');
        $otherParams = array();
        
        $obj = new StdSchoolStatus();
        $form = $this->createForm(new StdSchoolStatusType(), $obj);
        
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
        
        $obj = new StdSchoolStatus();
        $form = $this->createForm(new StdSchoolStatusType(), $obj);
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
        
        $otherParams['entity'] = $this->get('std_school_status_repository')->getStdSchoolStatus(array('by_id' => $id, 'action' => 'one'));
        $form = $this->createForm(new StdSchoolStatusType(), $otherParams['entity']);

        $params = $this->configureFormParams($request, array('form' => $form), $otherParams);
        if (isset($params['redirect'])) { return $this->redirect($params['redirect']); }

        return $this->render($params['page'], array('params' => $params));      
    }

    public function updateAction($id) {
        
        $request = $this->get('request');
        $otherParams = array();

        $otherParams['entity'] = $this->get('std_school_status_repository')->getStdSchoolStatus(array('by_id' => $id, 'action' => 'one'));

        $form = $this->createForm(new StdSchoolStatusType(), $otherParams['entity']);
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

    //function to perform adding and updating a school status
    public function processForm($form, $schoolStatusObj, $params) {

        if ($form->isValid()) {                  
          
            $em = $this->getDoctrine()->getManager();
            $em->persist($schoolStatusObj);
            $em->flush();
            
            if ($params['env'] == 'bo_') {
                $params['url_success'] = $this->generateUrl('bo_school_status_show', array('id' => $schoolStatusObj->getId()));
            } elseif ($params['env'] == 'mo_') {
                $params['url_success'] = $this->generateUrl('mo_school_status_show', array('id' => $schoolStatusObj->getId()));
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
        $schoolStatus = $em->getRepository('SOStandardBundle:StdSchoolStatus')->find($id);        
        if(is_object($schoolStatus) && $schoolStatus->getId()){
            $em->remove($schoolStatus);
            $em->flush(); // clear cached nodes
            return $this->redirect($this->generateUrl('bo_school_status_index'));  
        }else{
            return $this->redirect($this->generateUrl('bo_school_status_index',array('error'=>'Invalid Argument')));    
        }        
    }
    
    /***
     * Generic function for form that would be render commonly
     */
    private function configureFormParams($request, $par = array(), $objects = array())
    {
        // Initialize Array, form view and get environment
        $params = array();
        $params['route'] = array('element' => 'school_status', 'params' => array());
        $params['env'] = substr($request->attributes->get('_route'), 0, 3);
        $params['view'] = 'SOStandardBundle:StdSchoolStatus:form.html.twig';
        $params['form'] = $par['form']->createView();
       
        // Construct h1, route
        if (isset($objects['entity'])) {
            $params['route']['action'] = 'update';
            $params['h1'] = 'Modifier school status';
            $params['entity'] = $objects['entity'];
            $params['route']['params']['id'] = $objects['entity']->getId();
        } else {
            $params['route']['action'] = 'create';  
            $params['h1'] = 'Ajouter une school status';
        }

        // Get the good template
        $params = $this->get('standard_controller')->getGenericFormTemplate($request, $params);

        $params['url'] = $this->generateUrl($params['route']['name'], $params['route']['params']);

        return $params;
    }
}

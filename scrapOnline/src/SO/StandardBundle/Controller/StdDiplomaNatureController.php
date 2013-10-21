<?php

namespace SO\StandardBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use SO\StandardBundle\Entity\StdDiplomaNature;
use SO\StandardBundle\Form\StdDiplomaNatureType;
use SO\StandardBundle\Filter\StdDiplomaNatureFilterType;


class StdDiplomaNatureController extends Controller {

    public function indexAction() {
     
        $form = $this->get('form.factory')->create(new StdDiplomaNatureFilterType());
        $form->bind($this->get('request'));
        $conf = array();
        $p = $this->get('standard_controller')->initializeFilterAndPagination($this->get('request')->query->all(), $conf);           
        $diplomaNatures = $this->get('std_diploma_nature_repository')->getStdDiplomaNatures($p['params']);        
        
        $params = array(
            'elements' => $diplomaNatures,
            'params' => $p['params'],
            'filters' => array('fields' => $p['filters'], 'form' => $form->createView()) ,
            'h1' => 'Natures du diplôme de gestion',
            'view' => 'SOStandardBundle:StdDiplomaNature:_boIndex.html.twig',
            'addUrl' => 'bo_diploma_nature_new'
        );

        if($this->get('request')->isXmlHttpRequest()){
            return $this->render('SOStandardBundle:Backend:list.html.twig', $params);
        }else{
            return $this->render('SOStandardBundle:Backend:index.html.twig', $params);
        }
        
    }
    
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager();
        $diplomaNature = $em->getRepository('SOStandardBundle:StdDiplomaNature')->findOneById($id);
        return $this->render('SOStandardBundle:StdDiplomaNature:show.html.twig', array( 'diplomaNature' => $diplomaNature));           
    }
    
    public function newAction() {
        
        // Get base
        $request = $this->get('request');
        $otherParams = array();
        
        $obj = new StdDiplomaNature();
        $form = $this->createForm(new StdDiplomaNatureType(), $obj);
        
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
        
        $obj = new StdDiplomaNature();
        $form = $this->createForm(new StdDiplomaNatureType(), $obj);
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
        
        $otherParams['entity'] = $this->get('std_diploma_nature_repository')->getStdDiplomaNatures(array('by_id' => $id, 'action' => 'one'));
        $form = $this->createForm(new StdDiplomaNatureType(), $otherParams['entity']);

        $params = $this->configureFormParams($request, array('form' => $form), $otherParams);
        if (isset($params['redirect'])) { return $this->redirect($params['redirect']); }

        return $this->render($params['page'], array('params' => $params));        
    }

    public function updateAction($id) {
        
        $request = $this->get('request');
        $otherParams = array();

        $otherParams['entity'] = $this->get('std_diploma_nature_repository')->getStdDiplomaNatures(array('by_id' => $id, 'action' => 'one'));

        $form = $this->createForm(new StdDiplomaNatureType(), $otherParams['entity']);
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
    public function processForm($form, $diplomaNatureObj, $params) {

        if ($form->isValid()) {                  
          
            $em = $this->getDoctrine()->getManager();
            $em->persist($diplomaNatureObj);
            $em->flush();
            
            if ($params['env'] == 'bo_') {
                $params['url_success'] = $this->generateUrl('bo_diploma_nature_show', array('id' => $diplomaNatureObj->getId()));
            } elseif ($params['env'] == 'mo_') {
                $params['url_success'] = $this->generateUrl('mo_diploma_nature_show', array('id' => $diplomaNatureObj->getId()));
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
        $diplomaNature = $em->getRepository('SOStandardBundle:StdDiplomaNature')->find($id);        
        if(is_object($diplomaNature) && $diplomaNature->getId()){
            $em->remove($diplomaNature);
            $em->flush(); // clear cached nodes
            return $this->redirect($this->generateUrl('bo_diploma_nature_index'));  
        }else{
            return $this->redirect($this->generateUrl('bo_diploma_nature_index',array('error'=>'Invalid Argument')));    
        }        
    }
    
    
    /***
     * Generic function for form that would be render commonly
     */
    private function configureFormParams($request, $par = array(), $objects = array())
    {
        // Initialize Array, form view and get environment
        $params = array();
        $params['route'] = array('element' => 'diploma_nature', 'params' => array());
        $params['env'] = substr($request->attributes->get('_route'), 0, 3);
        $params['view'] = 'SOStandardBundle:StdDiplomaNature:form.html.twig';
        $params['form'] = $par['form']->createView();
       
        // Construct h1, route
        if (isset($objects['entity'])) {
            $params['route']['action'] = 'update';
            $params['h1'] = 'Modifier diplome nature';
            $params['entity'] = $objects['entity'];
            $params['route']['params']['id'] = $objects['entity']->getId();
        } else {
            $params['route']['action'] = 'create';  
            $params['h1'] = 'Ajouter une diplome nature';
        }

        // Get the good template
        $params = $this->get('standard_controller')->getGenericFormTemplate($request, $params);

        $params['url'] = $this->generateUrl($params['route']['name'], $params['route']['params']);

        return $params;
    }
}

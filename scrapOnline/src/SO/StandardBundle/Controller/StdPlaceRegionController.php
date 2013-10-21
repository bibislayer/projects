<?php

namespace SO\StandardBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use SO\StandardBundle\Entity\StdPlaceRegion;
use SO\StandardBundle\Form\StdPlaceRegionType;
use SO\StandardBundle\Filter\StdPlaceRegionFilterType;


class StdPlaceRegionController extends Controller {

    public function indexAction() {
     
        $form = $this->get('form.factory')->create(new StdPlaceRegionFilterType());
        $form->bind($this->get('request'));
        $conf = array();
        $p = $this->get('standard_controller')->initializeFilterAndPagination($this->get('request')->query->all(), $conf);        
        $regions = $this->get('std_place_region_repository')->getStdPlaceRegions($p['params']);
        
        $params = array(
            'elements' => $regions,
            'params' => $p['params'],
            'filters' => array('fields' => $p['filters'], 'form' => $form->createView()) ,
            'h1' => 'Place region management',
            'view' => 'SOStandardBundle:StdPlaceRegion:_boIndex.html.twig',
            'addUrl' => 'bo_region_new'
        );

        if($this->get('request')->isXmlHttpRequest()){
            return $this->render('SOStandardBundle:Backend:list.html.twig', $params);
        }else{
            return $this->render('SOStandardBundle:Backend:index.html.twig', $params);
        }
        
    }
    
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager();
        $region = $em->getRepository('SOStandardBundle:StdPlaceRegion')->findOneById($id);
        return $this->render('SOStandardBundle:StdPlaceRegion:show.html.twig', array( 'region' => $region));           
    }
    
    public function newAction() {
        
        // Get base
        $request = $this->get('request');
        $otherParams = array();
        
        $obj = new StdPlaceRegion();
        $form = $this->createForm(new StdPlaceRegionType(), $obj);
        
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
        
        $obj = new StdPlaceRegion();
        $form = $this->createForm(new StdPlaceRegionType(), $obj);
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
        
        $otherParams['entity'] = $this->get('std_place_region_repository')->getStdPlaceRegions(array('by_id' => $id, 'action' => 'one'));
        $form = $this->createForm(new StdPlaceRegionType(), $otherParams['entity']);

        $params = $this->configureFormParams($request, array('form' => $form), $otherParams);
        if (isset($params['redirect'])) { return $this->redirect($params['redirect']); }

        return $this->render($params['page'], array('params' => $params));       
    }

    public function updateAction($id) {
        
        $request = $this->get('request');
        $otherParams = array();

        $otherParams['entity'] = $this->get('std_place_region_repository')->getStdPlaceRegions(array('by_id' => $id, 'action' => 'one'));

        $form = $this->createForm(new StdPlaceRegionType(), $otherParams['entity']);
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

    //function to perform adding and updating a region
    public function processForm($form, $placeRegionObj, $params) {

         if ($form->isValid()) {                  
          
            $em = $this->getDoctrine()->getManager();
            $em->persist($placeRegionObj);
            $em->flush();
            
            if ($params['env'] == 'bo_') {
                $params['url_success'] = $this->generateUrl('bo_region_show', array('id' => $placeRegionObj->getId()));
            } elseif ($params['env'] == 'mo_') {
                $params['url_success'] = $this->generateUrl('mo_region_show', array('id' => $placeRegionObj->getId()));
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
        $region = $em->getRepository('SOStandardBundle:StdPlaceRegion')->find($id);        
        if(is_object($region) && $region->getId()){
            $em->remove($region);
            $em->flush(); // clear cached nodes
            return $this->redirect($this->generateUrl('bo_region_index'));  
        }else{
            return $this->redirect($this->generateUrl('bo_region_index',array('error'=>'Invalid Argument')));    
        }        
    }
    
    
    /***
     * Generic function for form that would be render commonly
     */
    private function configureFormParams($request, $par = array(), $objects = array())
    {
        // Initialize Array, form view and get environment
        $params = array();
        $params['route'] = array('element' => 'region', 'params' => array());
        $params['env'] = substr($request->attributes->get('_route'), 0, 3);
        $params['view'] = 'SOStandardBundle:StdPlaceRegion:form.html.twig';
        $params['form'] = $par['form']->createView();
       
        // Construct h1, route
        if (isset($objects['entity'])) {
            $params['route']['action'] = 'update';
            $params['h1'] = 'Modifier place region';
            $params['entity'] = $objects['entity'];
            $params['route']['params']['id'] = $objects['entity']->getId();
        } else {
            $params['route']['action'] = 'create';  
            $params['h1'] = 'Ajouter une place region';
        }

        // Get the good template
        $params = $this->get('standard_controller')->getGenericFormTemplate($request, $params);

        $params['url'] = $this->generateUrl($params['route']['name'], $params['route']['params']);

        return $params;
    }
}

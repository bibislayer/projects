<?php

namespace SO\StandardBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use SO\StandardBundle\Entity\StdBenefit;
use SO\StandardBundle\Form\StdBenefitType;
use SO\StandardBundle\Filter\StdBenefitFilterType;


class StdBenefitController extends Controller {

    public function indexAction() {
     
        $form = $this->get('form.factory')->create(new StdBenefitFilterType());
        $form->bind($this->get('request'));
        
        $conf = array();

        $p = $this->get('standard_controller')->initializeFilterAndPagination($this->get('request')->query->all(), $conf);
        $benefits = $this->get('std_benefit_repository')->getStdBenefits($p['params']);
        
        $params = array(
            'elements' => $benefits,
            'params' => $p['params'],
            'filters' => array('fields' => $p['filters'], 'form' => $form->createView()) ,
            'h1' => 'Les Atouts List',
            'view' => 'SOStandardBundle:StdBenefit:_boIndex.html.twig',
            'addUrl' => 'bo_benefit_new'
        );

        if($this->get('request')->isXmlHttpRequest()){
            return $this->render('SOStandardBundle:Backend:list.html.twig', $params);
        }else{
            return $this->render('SOStandardBundle:Backend:index.html.twig', $params);
        }
    }
    
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager();
        $benefit = $em->getRepository('SOStandardBundle:StdBenefit')->findOneById($id);
        return $this->render('SOStandardBundle:StdBenefit:show.html.twig', array( 'benefit' => $benefit));           
    }
    
    public function newAction() {
        
        // Get base
        $request = $this->get('request');
        $otherParams = array();
        
        $obj = new StdBenefit();
        $form = $this->createForm(new StdBenefitType(), $obj);
        
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
        
        $obj = new StdBenefit();
        $form = $this->createForm(new StdBenefitType(), $obj);
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
        
        $otherParams['entity'] = $this->get('std_benefit_repository')->getStdBenefits(array('by_id' => $id, 'action' => 'one'));
        $form = $this->createForm(new StdBenefitType(), $otherParams['entity']);

        $params = $this->configureFormParams($request, array('form' => $form), $otherParams);
        if (isset($params['redirect'])) { return $this->redirect($params['redirect']); }

        return $this->render($params['page'], array('params' => $params));               
    }

    public function updateAction($id) {        
        
        $request = $this->get('request');
        $otherParams = array();

        $otherParams['entity'] = $this->get('std_benefit_repository')->getStdBenefits(array('by_id' => $id, 'action' => 'one'));

        $form = $this->createForm(new StdBenefitType(), $otherParams['entity']);
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
    public function processForm($form, $benefitObj, $params) {
        
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();                      
            $em->persist($benefitObj);
            $em->flush();
            
            if ($params['env'] == 'bo_') {
                $params['url_success'] = $this->generateUrl('bo_benefit_show', array('id' => $benefitObj->getId()));
            } elseif ($params['env'] == 'mo_') {
                $params['url_success'] = $this->generateUrl('mo_benefit_show', array('slug' => $benefitObj->getId()));
            }
        } else {
            $params['errors'] = $form->getErrors();
        }

        return $params;     
    }
    
    
    
    public function removeAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $benefit = $em->getRepository('SOStandardBundle:StdBenefit')->find($id);        
        if(is_object($benefit) && $benefit->getId()){
            $em->remove($benefit);
            $em->flush(); // clear cached nodes
            return $this->redirect($this->generateUrl('bo_benefit_index'));  
        }else{
            return $this->redirect($this->generateUrl('bo_benefit_index',array('error'=>'Invalid Argument')));    
        }        
    }
    
    /***
     * Generic function for form that would be render commonly
     */
    private function configureFormParams($request, $par = array(), $objects = array())
    {
        // Initialize Array, form view and get environment
        $params = array();
        $params['route'] = array('element' => 'benefit', 'params' => array());
        $params['env'] = substr($request->attributes->get('_route'), 0, 3);
        $params['view'] = 'SOStandardBundle:StdBenefit:form.html.twig';
        $params['form'] = $par['form']->createView();
       
        // Construct h1, route
        if (isset($objects['entity'])) {
            $params['route']['action'] = 'update';
            $params['h1'] = 'Modifier benefit';
            $params['entity'] = $objects['entity'];
            $params['route']['params']['id'] = $objects['entity']->getId();
        } else {
            $params['route']['action'] = 'create';            
            $params['h1'] = 'Ajouter une benefit';
        }

        // Get the good template
        $params = $this->get('standard_controller')->getGenericFormTemplate($request, $params);

        $params['url'] = $this->generateUrl($params['route']['name'], $params['route']['params']);

        return $params;
    }
}

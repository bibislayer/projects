<?php

namespace SO\StandardBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use SO\StandardBundle\Entity\StdArticleType;
use SO\StandardBundle\Form\StdArticleTypeType;
use SO\StandardBundle\Filter\StdArticleTypeFilterType;


class StdArticleTypeController extends Controller {

    public function indexAction() {
     
        $form = $this->get('form.factory')->create(new StdArticleTypeFilterType());
        $form->bind($this->get('request'));
        $conf = array();
        $p = $this->get('standard_controller')->initializeFilterAndPagination($this->get('request')->query->all(), $conf);
        $articleTypes = $this->get('std_article_type_repository')->getStdArticleTypes($p['params']);
        $params = array(
            'elements' => $articleTypes,
            'params' => $p['params'],
            'filters' => array('fields' => $p['filters'], 'form' => $form->createView()) ,
            'h1' => 'Article type management',
            'view' => 'SOStandardBundle:StdArticleType:_boIndex.html.twig',
            'addUrl' => 'bo_article_type_new'
        );

        if($this->get('request')->isXmlHttpRequest()){
            return $this->render('SOStandardBundle:Backend:list.html.twig', $params);
        }else{
            return $this->render('SOStandardBundle:Backend:index.html.twig', $params);
        }
    }
    
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager();
        $articleType = $em->getRepository('SOStandardBundle:StdArticleType')->findOneById($id);
        return $this->render('SOStandardBundle:StdArticleType:show.html.twig', array( 'articleType' => $articleType));           
    }
    public function removeAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $articleType = $em->getRepository('SOStandardBundle:StdArticleType')->find($id);        
        if(is_object($articleType) && $articleType->getId()){
            $em->remove($articleType);
            $em->flush(); // clear cached nodes
            return $this->redirect($this->generateUrl('bo_article_type_index'));             
        }  else {
            return $this->redirect($this->generateUrl('bo_article_type_index',array('error'=>'Invalid Argument')));
        }        
    }
    
    public function newAction() {
        
        // Get base
        $request = $this->get('request');
        $otherParams = array();
        $articleTypeObj = new StdArticleType();
        $form = $this->createForm(new StdArticleTypeType(), $articleTypeObj);
        
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
        
        $articleTypeObj = new StdArticleType();
        $form = $this->createForm(new StdArticleTypeType(), $articleTypeObj);
        $form->handleRequest($request);
        
        // Get the configuration for the form
        $params = $this->configureFormParams($request, array('form' => $form), $otherParams);
        if (isset($params['redirect'])) { return $this->redirect($params['redirect']); }

        // Make Validation Part
        $params = $this->processForm($form, $articleTypeObj, $params);

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
        
        $otherParams['entity'] = $this->get('std_article_type_repository')->getStdArticleTypes(array('by_id' => $id, 'action' => 'one'));
        $form = $this->createForm(new StdArticleTypeType(), $otherParams['entity']);

        $params = $this->configureFormParams($request, array('form' => $form), $otherParams);
        if (isset($params['redirect'])) { return $this->redirect($params['redirect']); }

        return $this->render($params['page'], array('params' => $params));       
          
    }

    public function updateAction($id) {       
        
        $request = $this->get('request');
        $otherParams = array();

        $otherParams['entity'] = $this->get('std_article_type_repository')->getStdArticleTypes(array('by_id' => $id, 'action' => 'one'));

        $form = $this->createForm(new StdArticleTypeType(), $otherParams['entity']);
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
    public function processForm($form, $articleTypeObj, $params) {

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();                      
            $em->persist($articleTypeObj);
            $em->flush();
            
            if ($params['env'] == 'bo_') {
                $params['url_success'] = $this->generateUrl('bo_article_type_show', array('id' => $articleTypeObj->getId()));
            } elseif ($params['env'] == 'mo_') {
                $params['url_success'] = $this->generateUrl('mo_article_type_show', array('slug' => $articleTypeObj->getId()));
            }
        } else {
            $params['errors'] = $form->getErrors();
        }

        return $params;
    }
    
    /***
     * Generic function for form that would be render commonly
     */
    private function configureFormParams($request, $par = array(), $objects = array())
    {
        // Initialize Array, form view and get environment
        $params = array();
        $params['route'] = array('element' => 'article_type', 'params' => array());
        $params['env'] = substr($request->attributes->get('_route'), 0, 3);
        $params['view'] = 'SOStandardBundle:StdArticleType:form.html.twig';
        $params['form'] = $par['form']->createView();
       
        // Construct h1, route
        if (isset($objects['entity'])) {
            $params['route']['action'] = 'update';
            $params['h1'] = 'Modifier article type';
            $params['entity'] = $objects['entity'];
            $params['route']['params']['id'] = $objects['entity']->getId();
        } else {
            $params['route']['action'] = 'create';            
            $params['h1'] = 'Ajouter une article type';
        }

        // Get the good template
        $params = $this->get('standard_controller')->getGenericFormTemplate($request, $params);

        $params['url'] = $this->generateUrl($params['route']['name'], $params['route']['params']);

        return $params;
    }
}

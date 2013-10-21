<?php

namespace SO\StandardBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use SO\StandardBundle\Entity\StdCategory;
use SO\StandardBundle\Form\StdCategoryType;
use SO\StandardBundle\Filter\StdCategoryFilterType;

class StdCategoryController extends Controller {

    public function indexAction() {
     
        $form = $this->get('form.factory')->create(new StdCategoryFilterType());
        $form->bind($this->get('request'));

        $conf = array('display' => array('by_status'));

        $p = $this->get('standard_controller')->initializeFilterAndPagination($this->get('request')->query->all(), $conf);
        $categories = $this->get('std_category_repository')->getCategory($p['params']);

        $params = array(
            'elements' => $categories,
            'params' => $p['params'],
            'filters' => array('fields' => $p['filters'], 'form' => $form->createView()) ,
            'h1' => 'Tous les secteurs',
            'view' => 'SOStandardBundle:StdCategory:_boIndex.html.twig',
            'addUrl' => 'bo_category_new'
        );

        if($this->get('request')->isXmlHttpRequest()){
            return $this->render('SOStandardBundle:Backend:list.html.twig', $params);
        }else{
            return $this->render('SOStandardBundle:Backend:index.html.twig', $params);
        }

    }

    public function showAction($id) {
        $em = $this->getDoctrine()->getManager();
        $category = $em->getRepository('SOStandardBundle:StdCategory')->findOneById($id);
        return $this->render('SOStandardBundle:StdCategory:show.html.twig', array( 'category' => $category));           
    }

    public function foShowAction($slug) {

        $em = $this->getDoctrine()->getManager();
        $category = $em->getRepository('SOStandardBundle:StdCategory')->findOneBySlug($slug);
        return $this->render('SOStandardBundle:StdCategory:foShow.html.twig', array('category' => $category));
    }
    public function foIndexAction() {

        $params=array();
        $categories = $this->get('std_category_repository')->getCategory($params);
        return $this->render('SOStandardBundle:StdCategory:foIndex.html.twig', array('categories' => $categories));

    }
    
    public function newAction(){
        
        // Get base
        $request = $this->get('request');
        $otherParams = array();
        
        $obj = new StdCategory();
        $form = $this->createForm(new StdCategoryType(), $obj);
        
        // Get the configuration for the form
        $params = $this->configureFormParams($request, array('form' => $form), $otherParams);
        if (isset($params['redirect'])) { return $this->redirect($params['redirect']); }
        
        if($request->get('cat_id')){
            $params['parentObj'] = $this->get('std_category_repository')->getCategory(array('by_id' => $request->get('cat_id'), 'action' => 'one'));
        }

        // Display page
        return $this->render($params['page'], array('params' => $params));       
    }
    
    public function createAction() {

        //Get base
        $request = $this->get('request');
        $otherParams = array();
        
        $obj = new StdCategory();
        $form = $this->createForm(new StdCategoryType(), $obj);
        $form->handleRequest($request);
        
        // Get the configuration for the form
        $params = $this->configureFormParams($request, array('form' => $form), $otherParams);
        if (isset($params['redirect'])) { return $this->redirect($params['redirect']); }

        // Make Validation Part
        $params = $this->processForm($form, $obj, $params);
        
        if($request->get('cat_id')){
            $params['parentObj'] = $this->get('std_category_repository')->getCategory(array('by_id' => $request->get('cat_id'), 'action' => 'one'));
        }
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
        
        $otherParams['entity'] = $this->get('std_category_repository')->getCategory(array('by_id' => $id, 'action' => 'one'));
        $form = $this->createForm(new StdCategoryType(), $otherParams['entity']);

        $params = $this->configureFormParams($request, array('form' => $form), $otherParams);
        if (isset($params['redirect'])) { return $this->redirect($params['redirect']); }

        return $this->render($params['page'], array('params' => $params));      
    }

    public function updateAction($id) {
        
        $request = $this->get('request');
        $otherParams = array();

        $otherParams['entity'] = $this->get('std_category_repository')->getCategory(array('by_id' => $id, 'action' => 'one'));

        $form = $this->createForm(new StdCategoryType(), $otherParams['entity']);
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
    public function processForm($form, $categoryObj, $params) {

        $request=$this->getRequest();
        $categorie= $request->get('category');
        
        if ($form->isValid()) {                   
          
            //in case of add child set parent
            if(array_key_exists('parent_id', $categorie) && $categorie['parent_id']!=''){                
                $category = $this->get('std_category_repository')->getCategory(array('by_id' => $categorie['parent_id'], 'action' => 'one')); 
                $categoryObj->setParent($category);
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($categoryObj);
            $em->flush();
            
            if ($params['env'] == 'bo_') {
                $params['url_success'] = $this->generateUrl('bo_category_show', array('id' => $categoryObj->getId()));
            } elseif ($params['env'] == 'mo_') {
                $params['url_success'] = $this->generateUrl('mo_category_show', array('slug' => $categoryObj->getSlug()));
            }
        } else {
            $params['errors'] = $form->getErrors();
        }

        return $params;
             
    }
    public function removeAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $category = $em->getRepository('SOStandardBundle:StdCategory')->find($id);
        if(is_object($category) && $category->getId()){
            $repo = $em->getRepository('SOStandardBundle:StdCategory');
            $repo->removeFromTree($category);
            $em->clear(); // clear cached nodes
            return $this->redirect($this->getRequest()->headers->get('referer'));    
            exit;
        }
        
    }
    public function changeStatusAction($id, $type){
        $em = $this->getDoctrine()->getManager();
        $category = $em->getRepository('SOStandardBundle:StdCategory')->find($id);
        if(is_object($category) && $category->getId()){
            if($type=='publish'){
                $category->setPublished(1);
                $category->setApprobation(1);
                $category->setPublishedAt(new \DateTime(date('Y-m-d H:m:s')));
            }else if($type=='unpublish'){
                $category->setPublished(0);
                $category->setApprobation(0);
            }
            $em->persist($category);
            $em->flush();
            return $this->redirect($this->generateUrl('bo_categories'));             
        }  else {
            return $this->redirect($this->generateUrl('bo_categories',array('error'=>'Invalid Argument')));   
        }  
    }

    /***
     * Generic function for form that would be render commonly
     */
    private function configureFormParams($request, $par = array(), $objects = array())
    {
        // Initialize Array, form view and get environment
        $params = array();
        $params['route'] = array('element' => 'category', 'params' => array());
        $params['env'] = substr($request->attributes->get('_route'), 0, 3);
        $params['view'] = 'SOStandardBundle:StdCategory:form.html.twig';
        $params['form'] = $par['form']->createView();
       
        // Construct h1, route
        if (isset($objects['entity'])) {
            $params['route']['action'] = 'update';
            $params['h1'] = 'Modifier une categorie';
            $params['entity'] = $objects['entity'];
            $params['route']['params']['id'] = $objects['entity']->getId();
        } else {
            $params['route']['action'] = 'create';  
            if($request->get('cat_id')){
                $params['h1'] = 'Ajouter une child category';
            }else {
                $params['h1'] = 'Ajouter une categorie';
            }
        }

        // Get the good template
        $params = $this->get('standard_controller')->getGenericFormTemplate($request, $params);

        $params['url'] = $this->generateUrl($params['route']['name'], $params['route']['params']);

        return $params;
    }
    
    
    
     // for parent category name
    public function parentCategoryAction($id){
        $em = $this->getDoctrine()->getManager();
        $category = $em->getRepository('SOStandardBundle:StdCategory')->findOneBy(array('id' => $id));

        $lft = $category->getLft();
        $rgt = $category->getRgt();
        $root_id = $category->getRootId();
  		
        //custom repository CategoryRepository
        $parent_name = $this->get('std_category_repository')->getParentNameTree($root_id, $lft, $rgt);    
        return new Response($parent_name);
    }
    
    //For getting subcategories
    public function subCategoryAction($id) {
        if($id){
            $em = $this->getDoctrine()->getManager();
            $category = $em->getRepository('SOStandardBundle:StdCategory')->findOneBy(array('id' => $id));

            $lft = $category->getLft();
            $rgt = $category->getRgt();
            $root_id = $category->getRootId();
            $level = $category->getLevel();
                                   
            //using service
            $new_cat = $this->get('std_category_repository')->getCategoryChildByOneLevel($lft ,$rgt,$root_id,$level);
            
            $child_array = array();
            //making array of subchile with key and name
            if (count($new_cat) > 0) {
                foreach ($new_cat as $cat) {
                     $child_array[$cat->getId() . '_' . $cat->getLevel()] = $cat->getName();
                }
            }
         
             return new Response(json_encode(array('cat_data' => $child_array,'count' => count($new_cat))));
        }else{
             return new Response(json_encode(array('cat_data' => array(),'count' => 0)));
        }
    }
}

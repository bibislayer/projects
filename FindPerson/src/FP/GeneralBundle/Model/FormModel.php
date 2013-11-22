<?php

namespace FP\GeneralBundle\Model;

use Symfony\Component\DependencyInjection\ContainerInterface;

class FormModel
{
    private $object;
    private $formType;
    private $form;
    private $view ='';
    private $route = array('params' => array());
    private $h1 = '';
    private $errors = '';
    private $extraObjects = array();
    private $extraParams = array();
    private $layout=true;

    /***
     * Construct the object get container, request, environment, current action and if ajax
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    function __construct(ContainerInterface $container) {
        $this->container = $container;
        $this->request = $container->get('request');
        // GET ENVIRONMENT
        $this->env = substr($this->request->attributes->get('_route'), 0, 3);
        $this->action = substr(strrchr($this->request->attributes->get('_route'), '_'), 1);
        $this->route['env'] = $this->env;
        //echo $this->request->getMethod();
        // GET IF REQUEST IS USING AJAX
        $this->ajax = ($this->request->isXmlHttpRequest())? true : false;
        
    }

    /***
     * Set the Object you want to modify
     * @param $object
     */
    public function setObject($object){
        $this->object = $object;

        if($this->object->getId()){
            $this->route['action'] = ($this->request->getMethod() == 'POST')?'update':'update';
        }else{
            $this->route['action'] = ($this->request->getMethod() == 'POST')?'create':'create';
        }
    }
    /***
     * Set the view of the form you want to display
     * @param $view
     */
    public function setView($view){
        $this->view = $view;
    }
    /***
     * This will set an h1 in the generic form
     * @param $h1
     */
    public function setH1($h1){
        $this->h1 = $h1;
    }

     /***
     * This will set an layout in the generic form
     * @param $layout
     */
    public function setLayout($layout){
        $this->layout = $layout;
    }
    
    /***
     * This will set an errors in the generic form
     * @param $errors
     */
    public function setErrors($errors){
        $this->errors = $errors;
    }

    
    /***
     * This will set extra parameters in the generic form
     * @param $errors
     */
    public function setExtraParams($params){
        $this->extraParams = $params;
    }
    
    /***
     * In case of extra params in url to next part, add params there
     * @param $params
     */
    public function setUrlParams($params){
        $this->route['params'] = $params;
    }

    /***
     * If you want to display use other object in the view add object with their name
     * @param $name
     * @param $object
     */
    public function addObject($name, $object){
        $this->extraObjects[$name] = $object;
    }

    /***
     * This will configure the form by passing the object to edit and the form associated
     * @param $form
     * @param $object
     */
    
    public function setForm($form, $object , $options=array()){
        $this->setObject($object);
        $this->formType = $form;
        if(!empty($options)){
            $this->form = $this->container->get('form.factory')->create($this->formType, $this->object , $options);
           
        }else{
            $this->form = $this->container->get('form.factory')->create($this->formType, $this->object);
        }
        if($this->request->getMethod() == 'POST'){
            $this->form->handleRequest($this->request);
        }
    }
    /***
     * This will set the current element, used for routing
     * @param $element
     */
    public function setElement($element){
        $this->element = $element;
        $this->route['element'] = $element;
    }
    /***
     * Return the params to transmit to the template
     * @return array
     */
    public function getParams(){
        $params = array();
        //$url = New UrlGenerator();
        //$url->generate($this->route['env'].$this->element.'_'.$this->route['action'], array()); exit;
        $params['url'] = $this->container->get('router')->generate($this->route['env'].$this->element.'_'.$this->route['action'], $this->route['params']);
        $params['view'] = $this->view;
        $params['h1'] = $this->h1;
        $params['form'] = $this->form->createView();
        $params['page'] = $this->getTemplate();
        $params['others'] = $this->extraObjects;
        $params['errors'] = $this->errors;
        $params['env'] = $this->route['env'];
        $params['extraParams'] = $this->extraParams;
        
        return array('params' => $params);
    }
    
    /***
     * Get the good template instead of the environement
     * @return string
     */
    public function getTemplate()
    {
        switch($this->env):
            case 'bo_':
                if($this->ajax || $this->layout==false):
                    return 'FPGeneralBundle:Default:form.html.twig';
                else:
                    return 'FPGeneralBundle:Back:containerForm.html.twig';
                endif;
                break;
            case 'mo_':
                if($this->ajax || $this->layout==false):
                    return 'FPGeneralBundle:Default:form.html.twig';
                else:
                    return 'FPGeneralBundle:Middle:containerForm.html.twig';
                endif;
                break;
            case 'fo_':
                if($this->ajax || $this->layout==false):
                    return 'FPGeneralBundle:Default:form.html.twig';
                else:
                    return 'FPGeneralBundle:Front:containerForm.html.twig';
                endif;
                break;
        endswitch;
    }
    /***
     * Get the Form
     * @return mixed
     */
    public function getForm()
    {
        return $this->form;
    }
    /***
     * Get the Object
     * @return mixed
     */
    public function getObject()
    {
        return $this->object;
    }
    /***
     * Get the current action
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }
    /***
     * Get the current environment
     * @return string
     */
    public function getEnv()
    {
        return str_replace('_', '', $this->env);
    }
}

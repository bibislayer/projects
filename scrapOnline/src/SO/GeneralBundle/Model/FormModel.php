<?php

namespace SO\GeneralBundle\Model;

use Symfony\Component\DependencyInjection\ContainerInterface;

class FormModel
{
    private $object;
    private $formType;
    private $form;
    private $view ='';
    private $route = array('params' => array());
    private $h1 = '';
    private $extraObjects = array();

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

    public function setObject($object){
        $this->object = $object;

        if($this->object->getId()){
            $this->route['action'] = ($this->request->getMethod() == 'POST')?'update':'update';
        }else{
            $this->route['action'] = ($this->request->getMethod() == 'POST')?'create':'create';
        }
    }
    public function setView($view){
        $this->view = $view;
    }
    public function setH1($h1){
        $this->h1 = $h1;
    }
    public function setUrlParams($params){
        $this->route['params'] = $params;
    }
    public function setFormType($form){
        $this->form = $form;
    }

    public function addObject($name, $object){
        $this->extraObjects[$name] = $object;
    }

    public function setForm($form, $object){
        $this->setObject($object);
        $this->formType = $form;
        $this->form = $this->container->get('form.factory')->create($this->formType, $this->object);
        if($this->request->getMethod() == 'POST'){
            $this->form->handleRequest($this->request);
        }
    }
    public function setElement($element){
        $this->element = $element;
        $this->route['element'] = $element;
    }
    public function getParams(){
        $params = array();
        //$url = New UrlGenerator();
        //$url->generate($this->route['env'].$this->element.'_'.$this->route['action'], array()); exit;
        $params['url'] = $this->container->get('router')->generate($this->route['env'].$this->element.'_'.$this->route['action'], $this->route['params']);
        $params['view'] = $this->view;
        $params['h1'] = $this->h1;
        $params['form'] = $this->form->createView();
        $params['page'] = $this->getTemplate();
        return array('params' => $params);
    }

    public function getTemplate()
    {
        switch($this->env):
            case 'bo_':
                if($this->ajax):
                    return 'SOGeneralBundle:Default:form.html.twig';
                else:
                    return 'SOGeneralBundle:Back:containerForm.html.twig';
                endif;
                break;
            case 'mo_':
                if($this->ajax):
                    return 'SOGeneralBundle:Default:form.html.twig';
                else:
                    return 'SOGeneralBundle:Middle:containerForm.html.twig';
                endif;
                break;
            case 'fo_':
                if($this->ajax):
                    return 'SOGeneralBundle:Default:form.html.twig';
                else:
                    return 'SOGeneralBundle:Front:containerForm.html.twig';
                endif;
                break;
        endswitch;
    }
    public function getForm()
    {
        return $this->form;
    }
    public function getObject()
    {
        return $this->object;
    }
    public function getAction()
    {
        return $this->action;
    }
}

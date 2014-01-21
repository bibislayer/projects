<?php

namespace VM\GeneralBundle\Model;

use Symfony\Component\DependencyInjection\ContainerInterface;

class ListModel
{
    private $defaultParams = array();
    private $baseParams = array();
    private $passedParams = array();
    private $queryParams = array();
    private $params = array();
    private $limit = 5;
    private $system = array('action' => 'paginate');
    private $template;
    private $elements;
    private $urlPaginate = array('route' => '', 'params' => array());
    private $paramsForTemplate = array();

    function __construct(ContainerInterface $container) {

        $this->params = $this->defaultParams;
        $this->setQueryParams(array('action' => 'paginate', 'limit' => 2));
        $this->container = $container;
        $this->request = $container->get('request');
        if($this->request->isXmlHttpRequest()){
            $this->ajax = true;
            $this->env = substr($this->request->attributes->get('_route'), 0, 2);
            $this->passedParams(json_decode($this->request->request->get('passedParams'), true));
        }else{
            $url = preg_replace('#^/app_dev.php#', '', $_SERVER["REQUEST_URI"]);
            if(preg_match('#^/backend#', $url)){
                $this->env = 'bo';
            }elseif(preg_match('#^/admin#', $url)){
                $this->env = 'mo';
            }else{
                $this->env = 'mo';
            }
            $this->ajax = false;
        }
    }

    public function defineBase($params = array()){
        $this->baseParams = $params;
        $this->params = array_merge($this->params, $this->baseParams);
        $this->treatParams();
    }

    public function passedParams($par = array()){
        $this->passedParams = array_merge($this->passedParams, $par);
        $this->params = array_merge($this->params, $this->passedParams);
        $this->treatParams();
    }

    public function setQueryParams($params){
        $this->queryParams = array_merge($this->queryParams, $params);
    }
    public function getQueryParams(){
        return $this->queryParams;
    }
    public function setTemplate($template){
        $this->template = $template;
    }
    public function getTemplate(){
        return $this->template;
    }
    public function getEnv(){
        return $this->env;
    }

    public function setUrlPaginate($urlPaginate){
        return $this->urlPaginate = $urlPaginate;
    }

    private function treatParams(){
        $params = $this->params;

        if(array_key_exists('template', $params) && $params['template']){
            $this->setTemplate($params['template']);
        }
        if(array_key_exists('urlPaginate', $params) && $params['urlPaginate']){
            $this->setUrlPaginate($params['urlPaginate']);
        }
        if(array_key_exists('title', $params) && $params['title']){
            $this->paramsForTemplate['title'] = $params['title'];
        }
        if(array_key_exists('paramsQ', $params) && $params['paramsQ']){
            $this->queryParams = array_merge($this->queryParams, $params['paramsQ']);
        }
    }

    public function setQuery($q)
    {
        if($q){
            $this->paramsForTemplate['pagination'] = true;
            $paginator  = $this->container->get('knp_paginator');
            $this->elements = $paginator->paginate($q, $this->request->query->get('page', 1), $this->limit);

            //$this->elements->setUsedRoute('mo_questionnaires_list');
        }
    }

    public function getParams(){
       //$this->urlPaginate = '';
       $this->paramsForTemplate = array_merge($this->paramsForTemplate, array('elements' => $this->elements, 'view' => $this->template, 'uniqueTok' => $this->get_random_string(8)));
       $this->paramsForTemplate = array_merge($this->paramsForTemplate, array('postParams' => json_encode($this->passedParams)));
       return $this->paramsForTemplate;
    }
    public function getReturn(){
        if($this->urlPaginate && $this->urlPaginate['route']){
            $this->elements->setUsedRoute($this->urlPaginate['route'], isset($this->urlPaginate['params'])?$this->urlPaginate['params']:'');
        }
       return $this->container->get('templating')->renderResponse('VMGeneralBundle:Default:listModel.html.twig', $this->getParams());
    }

    private function get_random_string($length, $valid_chars = 'abcdefghijklmnopqrstuvwxyz0123456789')
    {
        // start with an empty random string
        $random_string = "";

        // count the number of chars in the valid chars string so we know how many choices we have
        $num_valid_chars = strlen($valid_chars);

        // repeat the steps until we've created a string of the right length
        for ($i = 0; $i < $length; $i++)
        {
            // pick a random number from 1 up to the number of valid chars
            $random_pick = mt_rand(1, $num_valid_chars);

            // take the random character out of the string of valid chars
            // subtract 1 from $random_pick because strings are indexed starting at 0, and we started picking at 1
            $random_char = $valid_chars[$random_pick-1];

            // add the randomly-chosen char onto the end of our string so far
            $random_string .= $random_char;
        }

        // return our finished random string
        return $random_string;
    }
}

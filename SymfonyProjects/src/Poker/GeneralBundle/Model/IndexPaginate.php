<?php

namespace Poker\GeneralBundle\Model;

use Symfony\Component\DependencyInjection\ContainerInterface;

class IndexPaginate
{
    protected $container;
    private $allowed = array('limit' => '', 'by_keyword' => '');
    private $filters = array('base' => array('limit' => '', 'by_keyword' => ''));
    private $params = array();
    private $h1 = 'Tous les éléments';
    private $view = '';
    private $limit = 10;
    private $filterStrategy = 'both';
    private $addUrl;
    private $allowedStrategy = 'merge';
    private $queryParams = array('action' => 'paginate', 'limit' => 10);

    function __construct(ContainerInterface $container) {
        $this->container = $container;
        // GET REQUEST
        $this->request = $container->get('request');
        // GET PARAMS REQUEST
        $this->requestParams = $this->request->query->all();
        // GET IF REQUEST IS USING AJAX
        $this->ajax = ($this->request->isXmlHttpRequest())? true : false;
        // GET ENVIRONMENT
        $this->env = substr($this->request->attributes->get('_route'), 0, 3);
        // CHECK THE LIMIT
        if($this->request->query->get('limit')){
            $this->limit = $this->filters['base']['limit']['value'] = $this->request->query->get('limit');
        }
        if($this->request->query->get('by_keyword')){
            $this->filters['base']['by_keyword']['value'] = $this->request->query->get('by_keyword');
        }
    }

    /***
     * Set H1 for index
     * @param $h1
     */
    public function setH1($h1)
    {
        $this->h1 = $h1;
    }

    /***
     * Set a link to add a new element
     * @param $route
     * @param null $title
     * @param array $params
     */
    public function setAddNew($route, $title = NULL, $params=array())
    {
        $url = array();
        $url['url'] = $this->container->get('router')->generate($route, $params);
        if($title){
            $url['title'] = $title;
        }
        $this->addUrl[] = $url;
    }
    
    /***
     * Set the view of the index
     * REQUIRED
     * @param $view
     */
    public function setView($view)
    {
        if($view)
            $this->view = $view;
    }
    /***
     * Set the filters to display
     * @param $filters
     */
    public function setFilters($filters)
    {
        if($filters){
            foreach($filters as $k => $filter){
                if(is_array($filter)){
                    $this->filters['others'][$k] = $filter;}
                else{
                    $this->filters['others'][$filter] = array();}
            }
        }
    }
    /***
     * Set filter strategy
     * @param $filterStrategy
     */
    public function setFilterStrategy($filterStrategy)
    {
        // Check for the base filter
        switch($filterStrategy):
            case 'both':
                break;
            case 'limit';
                unset($this->filters['base']['by_keyword']);
                unset($this->allowed['by_keyword']);
                break;
            case 'by_keyword';
                unset($this->filters['base']['limit']);
                unset($this->allowed['limit']);
                break;
            case 'none';
                $this->filters['base'] = array();
                unset($this->allowed['limit']);
                unset($this->allowed['by_keyword']);
                break;
        endswitch;

    }
    /***
     * Set configuration of the filter
     * @param $conf
     */
    public function configureFilters($conf)
    {
        if(isset($conf['base'])){
            $this->setFilterStrategy($conf['base']);
        }
    }
    /***
     * Set configuration of the filter
     * @param $filters
     */
    public function addFilters($form, $filters)
    {
        $this->form = $this->container->get('form.factory')->create($form);
        $this->form->bind($this->request);

        foreach($filters as $kd => $d){
            if(is_array($d)){
                $this->filters['others'][$kd] = $d;}
            else{
                $this->filters['others'][$d] = array();}
        }
        if($this->allowedStrategy == 'merge'){
            $this->allowed = array_merge($this->allowed, $this->filters['others']);
        }
    }
    /***
     * Add additionnal params to the query
     * @param $params
     */
    public function addQueryParams($params = array())
    {
        $this->queryParams = array_merge($this->queryParams, $params);
    }
    /***
     * Add additionnal params to template
     * @param $params
     */
    public function addParams($params = array())
    {
        $this->params = array_merge($this->params, $params);
    }
    /***
     * Treat the query
     * REQUIRED
     * @param $q
     */
    public function setQuery($q)
    {
        if($q){
            $paginator  = $this->container->get('knp_paginator');
            $this->elements = $paginator->paginate($q, $this->request->query->get('page', 1), $this->limit);
        }
    }

    /***
     * Get params for the view
     * @return array
     */
    public function getParams()
    {
        if(isset($this->view)){
            $this->params['view'] = $this->view;
        }else{
            echo 'No template view defined';
            exit;
        }
        if(isset($this->elements)){
            $this->params['elements'] = $this->elements;
        }else{
            echo 'No SQL Request defined';
            exit;
        }
        if($this->addUrl)
            $this->params['addUrl'] = $this->addUrl;
        if(isset($this->form))
            $this->params['form'] = $this->form->createView();

        $this->params['h1'] = $this->h1;
        $this->params['filters'] = array('fields' => $this->filters);

        if($this->ajax){
            return $this->params;
        }else{
            return array('params' => $this->params);
        }
    }

    /***
     * Get the Params for the query
     */
    public function getParamsForQuery()
    {
        if($this->allowed){
            $this->getAllowedParamsFromRequest($this->requestParams);
        }
        return $this->queryParams;
    }

    /***
     * Get the template for the view
     * This is analysis if from ajax request or not
     * @return string
     */
    public function getTemplate()
    {
        switch($this->env):
            case 'bo_':
                if($this->ajax):
                    return 'PokerGeneralBundle:Default:list.html.twig';
                else:
                    return 'PokerGeneralBundle:Back:containerList.html.twig';
                endif;
                break;
            case 'mo_':
                if($this->ajax):
                    return 'PokerGeneralBundle:Default:list.html.twig';
                else:
                    return 'PokerGeneralBundle:Middle:containerList.html.twig';
                endif;
                break;
            case 'fo_':
                if($this->ajax):
                    return 'PokerGeneralBundle:Default:list.html.twig';
                else:
                    return 'PokerGeneralBundle:Front:containerList.html.twig';
                endif;
                break;
        endswitch;
    }

    /***
     * Get back the params and compare with the allowed Params
     * @param $req
     */
    private function getAllowedParamsFromRequest($req){

        foreach($req as $k => $param):
            if($param != ''){
                if(isset($this->allowed[$k])){
                    $this->queryParams[$k] = $param;
                }elseif(is_array($param)){
                    $this->getAllowedParamsFromRequest($param);
                }
            }
        endforeach;
    }

}

<?php

namespace SO\StandardBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;


class DefaultController extends Controller {

    public function indexAction($type) {
        $request = $this->getRequest();
        $q = $request->get('term');
        $limit = $request->get('limit');
        $callback = $request->get('callback');
        if($type=='Enterprise'){
             $data = $this->getDoctrine()->getEntityManager()->createQuery("SELECT e.id, e.name AS name FROM SOEnterpriseBundle:".$type." e WHERE e.name LIKE :q")
                ->setParameter('q', "" . $q . "%")
                ->setMaxResults($limit)
                ->getArrayResult();
        }else if($type=='Diploma'){
            if($request->get('typed')){
               $data = $this->getDoctrine()->getEntityManager()->createQuery("SELECT d.id, d.name AS name FROM SODiplomaBundle:".$type." d WHERE d.name LIKE :q and d.TypeDiplome='".$request->get('typed')."'")
                ->setParameter('q', "" . $q . "%")
                ->setMaxResults($limit)
                ->getArrayResult();
           }else{
             $data = $this->getDoctrine()->getEntityManager()->createQuery("SELECT d.id, d.name AS name FROM SODiplomaBundle:".$type." d WHERE d.name LIKE :q")
                ->setParameter('q', "" . $q . "%")
                ->setMaxResults($limit)
                ->getArrayResult();
           }   
        }else if($type=='Profession'){
             $data = $this->getDoctrine()->getEntityManager()->createQuery("SELECT p.id, p.name AS name FROM SOProfessionBundle:".$type." p WHERE p.name LIKE :q")
                ->setParameter('q', "" . $q . "%")
                ->setMaxResults($limit)
                ->getArrayResult();
        }else{
            $data = $this->getDoctrine()->getEntityManager()->createQuery("SELECT e.id, e.name AS name FROM SOStandardBundle:".$type." e WHERE e.name LIKE :q")
                ->setParameter('q', "" . $q . "%")
                ->setMaxResults($limit)
                ->getArrayResult();
        }
        $arrayNew = array("success" => $data);

        $response = new JsonResponse($arrayNew, 200, array());
        $response->setCallback($callback);
        $response->headers->set('Content-Type', 'application/json;charset=UTF-8');

        return $response;
    }

    //Action for adding data into table if not exists , returns id else returns id by name 
    public function addAction($type){
        $request = $this->getRequest();
        $name = $request->get('name');
        $em = $this->getDoctrine()->getManager();
        
        //finds if already exists this 
        if($type=='Diploma'){
            $data = $em->getRepository('SODiplomaBundle:'.$type)->findOneBy(array('name' => $name));
        }else if($type=='Profession'){
            $data = $em->getRepository('SOProfessionBundle:'.$type)->findOneBy(array('name' => $name));
        }else{
            $data = $em->getRepository('SOStandardBundle:'.$type)->findOneBy(array('name' => $name));
        }
        //if exists
        if ($data){           
             $arrayNew = array("id" => $data->getId());
        }else {
            if($type!='Profession'){
                $type = "SO\\StandardBundle\\Entity"."\\".$type;

                $type_instance = new $type();

                $type_instance->setName($name);      

                $em->persist($type_instance);
                $em->flush();

                $arrayNew = array('id' => $type_instance->getId());
            
            }else{
                $arrayNew = array('id' => 0); 
            }
        }
               
        $response = new JsonResponse($arrayNew, 200, array());
        $response->headers->set('Content-Type', 'application/json;charset=UTF-8');
        
        return $response;
    }
    public function standardsAction() {
        $params = array('actions' => 'count');
        $count = array();
        $count['diplomaEtat'] = $this->get('std_diploma_nature_repository')->getStdDiplomaNatures($params);
        $count['diplomaLevel'] = $this->get('std_diploma_level_repository')->getStdDiplomaLevels($params);
        $count['diplomaType'] = $this->get('std_diploma_type_repository')->getStdDiplomaTypes($params);
        $count['category'] = $this->get('std_category_repository')->getCategory($params);
        $count['schoolStatus'] = $this->get('std_school_status_repository')->getStdSchoolStatus($params);
        $count['schoolType'] = $this->get('std_school_type_repository')->getStdSchoolTypes($params);
        $count['socialStatus'] = $this->get('std_social_status_repository')->getStdSocialStatus($params);
        $count['associationType'] = $this->get('std_association_type_repository')->getStdAssociationTypes($params);
        $count['eventType'] = $this->get('std_event_type_repository')->getStdEventTypes($params);
        $count['country'] = $this->get('std_place_country_repository')->getStdPlaceCountries($params);
        $count['city'] = $this->get('std_place_city_repository')->getStdPlaceCitys($params);
        $count['region'] = $this->get('std_place_region_repository')->getStdPlaceRegions($params);
        $count['department'] = $this->get('std_place_department_repository')->getStdPlaceDepartments($params);
        $count['skill'] = $this->get('std_skill_repository')->getStdSkills($params);
        $count['goal'] = $this->get('std_goal_repository')->getStdGoals($params);
        $count['benefit'] = $this->get('std_benefit_repository')->getStdBenefits($params);
        $count['quality'] = $this->get('std_quality_repository')->getStdQualitys($params);
        
        return $this->render('SOStandardBundle:Default:standards.html.twig', array('count' => $count));
     }

    public function initializeFilterAndPagination($req, $config = array()) {

        $p = array('params' => array(), 'filters' => array());
        $allowed = array();
        $config['base'] = (isset($config['base'])) ? $config['base'] : 'both';
        $p['params'] = array('actions' => 'paginate', 'page' => 1, 'max_items_on_listepage' => 10);

        // Check for the base filter
        switch($config['base']):
            case 'both':
                $p['filters']['base'] = array('limit' => '', 'by_keyword' => '');
            break;
            case 'limit';
                $p['filters']['base'] = array('limit' => '');
            break;
            case 'by_keyword';
                $p['filters']['base'] = array('by_keyword' => '');
            break;
        endswitch;

        // Check for page
        if(isset($req['page']) && is_numeric($req['page'])){
            $p['params']['page'] = $req['page'];
        }
        // Check for the limit and keyword
        if(isset($p['filters']['base'])){
            if(isset($p['filters']['base']['limit'])&& isset($req['limit']) && is_numeric($req['limit'])){
                $p['params']['max_items_on_listepage'] = $req['limit'];
                $p['filters']['base']['limit']['value'] = $p['params']['max_items_on_listepage'];
            }
            if(isset($p['filters']['base']['by_keyword']) && isset($req['by_keyword']) && $req['by_keyword'] != ''){
                $p['params']['by_keyword'] = $req['by_keyword'];
                $p['filters']['base']['by_keyword']['value'] = $p['params']['by_keyword'];
            }
        }

        // Check for the allowed params
        if(isset($config['allowed'])){
            foreach($config['allowed'] as $kdi => $di){
                if(is_array($di))
                    $allowed[$kdi] = $di;
                else
                    $allowed[$di] = array();
            }
        }
        // Check and add extra filters
        if(isset($config['display'])){
            foreach($config['display'] as $kd => $d){
                if(is_array($d)){
                    $p['filters']['others'][$kd] = $d;}
                else{
                    $p['filters']['others'][$d] = array();}
            }
            // Check if display filters are allowed to params
            if(!isset($config['strategy']) || (isset($config['strategy']) && $config['strategy'] == 'merge')){
                $allowed = array_merge($allowed, $p['filters']['others']);
            }
        }
//echo '<pre>';print_r($allowed);
        // Check if extra params are allowed
        if($allowed){
            // Retrieve params and check allowed
            $p['params'] = $this->getAllowedParamsFromRequest($req, $p['params'], $allowed);
        }

        return $p;
    }

    private function getAllowedParamsFromRequest($req, $params, $allowed){

        foreach($req as $k => $param):
            if($param != ''){
                if(is_array($param)){
                    // If find an array do another foreach
                    $params = $this->getAllowedParamsFromRequest($param, $params, $allowed);
                }else{
                    if(isset($allowed[$k])){
                        $params[$k] = $param;
                    }
                }

            }
        endforeach;

        return $params;

    }

    public function getGenericFormTemplate($request, $params) {
        //echo '<pre>'; print_r($params); exit;
        $params['route']['name'] = $params['env'].$params['route']['element'].'_'.$params['route']['action'];

        if($params['env'] == 'bo_'){
            if($request->isXmlHttpRequest() || (isset($params['layout']) && $params['layout'] == false)){
                $params['page'] = 'SOStandardBundle:Backend:form.html.twig';
            }else{
                $params['page'] = 'SOStandardBundle:Backend:containerForm.html.twig';
            }
        }elseif($params['env'] == 'mo_'){
            if($request->isXmlHttpRequest() || (isset($params['layout']) && $params['layout'] == false)){
                $params['page'] = 'SOStandardBundle:Backend:form.html.twig';
            }else{
                $params['page'] = 'SOMidBundle::containerForm.html.twig';
            }
        }elseif($params['env'] == 'fo_'){
            if($request->isXmlHttpRequest() || (isset($params['layout']) && $params['layout'] == false)){
                $params['page'] = 'SOStandardBundle:Backend:form.html.twig';
            }else{
                $params['page'] = 'SOStandardBundle:Backend:containerForm.html.twig';
            }
        }

        return $params;

    }
}

<?php

namespace VM\MakerBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class AjaxController extends Controller
{
    
    public function makerAutocompleteAction() {

        $request = $this->getRequest();
        $q = $request->get('q');
        $limit = $request->get('limit');
        $callback = $request->get('callback');

        $data = $this->getDoctrine()->getEntityManager()->createQuery("SELECT e.id, e.name AS name FROM VMMakerBundle:Maker e WHERE e.name LIKE :q")
           ->setParameter('q', "" . $q . "%")
           ->setMaxResults($limit)
           ->getArrayResult();

        $arrayNew = array("success" => $data);

        $response = new JsonResponse($arrayNew, 200, array());
        $response->setCallback($callback);
        $response->headers->set('Content-Type', 'application/json;charset=UTF-8');

        return $response;
    }
    
    public function updateSattusAction(){
        $request = $this->getRequest();
        if($request->get('id')!='' && $request->get('type')!='' && $request->get('option')!=''){
            $maker = $this->get('maker_repository')->getElements(array('by_id' => $request->get('id'), 'action' => 'one'));
            $statusArray = $maker->getStatus();
            $statusArray[$request->get('type')] = $request->get('option');
            $maker->setStatus($statusArray);
            $em=$this->getDoctrine()->getEntityManager();
            $em->persist($maker);
            $em->flush();
            $data=true;
        }else{
            $data=false;
        }
        $arrayNew = array("success" => $data);     

        $response = new JsonResponse($arrayNew, 200, array());
        $response->headers->set('Content-Type', 'application/json;charset=UTF-8');

        return $response;
    }

}

?>
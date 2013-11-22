<?php

namespace FP\MailerBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class AjaxController extends Controller
{
    //to show mailer category by id
    public function mailerCategoryAutocompleteAction() {

        $request = $this->getRequest();
        $q = $request->get('q');
        $limit = $request->get('limit');
        $callback = $request->get('callback');

        $data = $this->getDoctrine()->getEntityManager()->createQuery("SELECT e.id, e.name AS name FROM FPMailerBundle:MailerCategory e WHERE e.name LIKE :q")
           ->setParameter('q', "" . $q . "%")
           ->setMaxResults($limit)
           ->getArrayResult();

        $arrayNew = array("success" => $data);

        $response = new JsonResponse($arrayNew, 200, array());
        $response->setCallback($callback);
        $response->headers->set('Content-Type', 'application/json;charset=UTF-8');

        return $response;
    }

}

?>
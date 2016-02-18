<?php

namespace FAC\GeneralBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Component\HttpFoundation\JsonResponse;

use FAC\GeneralBundle\Entity\Feedback;

class DefaultController extends Controller
{
    /**
     * @Route("/hello/{name}")
     * @Template()
     */
    public function indexAction($name)
    {
        return array('name' => $name);
    }

    /**
     * @Template()
     */
    public function navbarAction($route, $files)
    {
    	$em = $this->getDoctrine()->getManager();
    	//$files = $em->getRepository('FACFileBundle:File')->findBy(array('user' => $this->get('security.context')->getToken()->getUser(), 'type' => 'Directory'));

        return array('files' => $files, 'route' => $route);
    }

    /**
     * @Route("/send-feedback")
     * @Template()
     */
    public function sendFeedbackAction()
    {
        if ($this->getRequest()->isMethod('POST')) {
            $em = $this->getDoctrine()->getManager();
            $usr = $this->get('security.context')->getToken()->getUser();

            $feedback = new Feedback($usr);
            $feedback->setComment($this->getRequest()->request->get('comment'));
            $feedback->setCategory($this->getRequest()->request->get('category'));
            $feedback->setStatus(0);

            $em->persist($feedback);
            $em->flush();

            return new JsonResponse(array(
                'success' => true
            ));
        }
    }
}

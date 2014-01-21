<?php

namespace VM\FeedbackBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('VMFeedbackBundle:Default:index.html.twig', array('name' => $name));
    }
}

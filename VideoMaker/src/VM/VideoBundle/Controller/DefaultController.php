<?php

namespace VM\VideoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('VMVideoBundle:Default:index.html.twig', array('name' => $name));
    }
}

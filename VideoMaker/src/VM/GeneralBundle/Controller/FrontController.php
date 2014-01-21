<?php

namespace VM\GeneralBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class FrontController extends Controller
{
    public function indexAction()
    {
        return $this->render('VMGeneralBundle:Front:index.html.twig', array());
    }
}

<?php

namespace FP\GeneralBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class FrontController extends Controller
{
    public function indexAction()
    {
        return $this->render('FPGeneralBundle:Front:index.html.twig', array());
    }
}

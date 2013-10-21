<?php

namespace SO\GeneralBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class FrontController extends Controller
{
    public function indexAction()
    {
        return $this->render('SOGeneralBundle:Front:index.html.twig', array());
    }
}

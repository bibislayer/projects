<?php

namespace FP\StandardBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('FPStandardBundle:Default:index.html.twig');
    }
}

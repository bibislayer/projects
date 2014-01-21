<?php

namespace VM\StandardBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('VMStandardBundle:Default:index.html.twig');
    }
}

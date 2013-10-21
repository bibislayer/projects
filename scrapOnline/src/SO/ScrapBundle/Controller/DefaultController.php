<?php

namespace SO\ScrapBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('SOScrapBundle:Default:index.html.twig', array('name' => $name));
    }
}

<?php

namespace FP\MailerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('FPMailerBundle:Default:index.html.twig', array('name' => $name));
    }
}

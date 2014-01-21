<?php

namespace VM\GeneralBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BackController extends Controller
{
    public function dashboardAction()
    {
        return $this->render('VMGeneralBundle:Back:dashboard.html.twig');
    }
}

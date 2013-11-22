<?php

namespace FP\GeneralBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BackController extends Controller
{
    public function dashboardAction()
    {
        return $this->render('FPGeneralBundle:Back:dashboard.html.twig');
    }
}

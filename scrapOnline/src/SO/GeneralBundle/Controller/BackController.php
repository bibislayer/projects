<?php

namespace SO\GeneralBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BackController extends Controller
{
    public function dashboardAction()
    {
        return $this->render('SOGeneralBundle:Back:dashboard.html.twig');
    }
}

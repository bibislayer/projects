<?php

namespace Poker\GeneralBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BackController extends Controller
{
    public function dashboardAction()
    {
        return $this->render('PokerGeneralBundle:Back:dashboard.html.twig');
    }
}

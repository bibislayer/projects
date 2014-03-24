<?php

namespace Poker\GeneralBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class FrontController extends Controller
{
    public function indexAction()
    {
        return $this->render('PokerGeneralBundle:Front:index.html.twig', array());
    }
}

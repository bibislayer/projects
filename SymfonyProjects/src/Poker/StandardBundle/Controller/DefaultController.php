<?php

namespace Poker\StandardBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('PokerStandardBundle:Default:index.html.twig');
    }
}

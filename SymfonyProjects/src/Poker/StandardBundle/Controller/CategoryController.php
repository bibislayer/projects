<?php

namespace Poker\StandardBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CategoryController extends Controller
{
    public function boIndexAction()
    {
        return $this->render('PokerStandardBundle:Category:index.html.twig', array());
    }
    
    public function boShowAction()
    {
        return $this->render('PokerStandardBundle:Category:show.html.twig', array());
    }
    
    public function boDeleteAction()
    {
        //delete an category
    }
}

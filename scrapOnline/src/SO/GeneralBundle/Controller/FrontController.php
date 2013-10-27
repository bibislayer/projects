<?php

namespace SO\GeneralBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class FrontController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $movies = $em->getRepository('SOMovieBundle:Movie')->findBy(array(), array('publicRating' => 'DESC'), 11);
        
        return $this->render('SOGeneralBundle:Front:index.html.twig', array('movies' => $movies));
    }
}

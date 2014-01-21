<?php

namespace SO\CameraBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($slug)
    {
        $em = $this->getDoctrine()->getManager();
        $movie = $em->getRepository('SOMovieBundle:Movie')->findOneBy(array('slug' => $slug));

        return $this->render('SOCameraBundle:Default:index.html.twig', array('movie' => $movie));
    }
}

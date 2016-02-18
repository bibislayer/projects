<?php

namespace FAC\HomeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="home")
     * @Template()
     */
    public function indexAction()
    {
        if($this->getRequest()->isMethod('HEAD')){
            header('Content-type: text/html');
            echo 'success';
            exit;
        }
        return array('title' => 'Accueil',
        			'template' => 'noMenu',
        			'h1' => 'Tableau de bord',
        			'bugs' => array(),
        			'sondages' => array(),
        			'menuShow' => 'partial',
        			/*'message' => array(
        					'type' => 'warning', 
        					'text' => 'test'
    					)*/
        			);
    }
}

<?php

namespace VM\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class UserController extends Controller {

    public function dashboardAction() {
        $user = $this->get('security.context')->getToken()->getUser();
        $questionnairesUser = $user->getQuestionnaire();
        return $this->render('VMUserBundle:Middle:dashboard.html.twig', array('questionnairesUser' => $questionnairesUser));
    }

}

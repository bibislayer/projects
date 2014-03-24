<?php


namespace Poker\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller managing the backend
 *
 */
class BackController extends Controller {

    public function boIndexAction() {
        $paginate = $this->get("index_paginate");
        $paginate->setH1('Tous les utilisateurs');
        $paginate->setView('PokerUserBundle:User/Back:index.html.twig');

        $query = $this->get('user_repository')->getUsers($paginate->getParamsForQuery());

        $paginate->setQuery($query);
        return $this->render($paginate->getTemplate(), $paginate->getParams());
    }

    public function boShowAction($id) {
        $user = $this->get('user_repository')->getUsers(array('by_id' => $id, 'action' => 'one'));

        if($user){
            $breadcrumbs = $this->get("white_october_breadcrumbs");
            $breadcrumbs->addItem("Tous les utilisateurs", $this->get("router")->generate("bo_users"));
            $breadcrumbs->addItem('');
            return $this->render('PokerUserBundle:User/Back:show.html.twig', array( 'user' => $user));
        }else{

        }
    }

}

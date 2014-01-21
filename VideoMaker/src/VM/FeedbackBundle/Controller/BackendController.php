<?php

namespace VM\FeedbackBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BackendController extends Controller {

    public function boIndexAction() {
        $request= $this->getRequest();
        $paginate = $this->get("index_paginate");
        $paginate->setH1('Tous les feedback');
        $paginate->setView('VMFeedbackBundle:Backend:index.html.twig');
        $paginate->setAddNew('bo_feedback_new');
        $query = $this->get('feedback_repository')->getElements($paginate->getParamsForQuery());

        $paginate->setQuery($query);
        return $this->render($paginate->getTemplate(), $paginate->getParams());
    }

    public function boShowAction($id) {
        $feedback = $this->get('feedback_repository')->getElements(array('by_id' => $id, 'action' => 'one'));
        if($feedback){
            $breadcrumbs = $this->get("white_october_breadcrumbs");
            $breadcrumbs->addItem("Tous les feedback", $this->get("router")->generate("bo_feedbacks"));
            $breadcrumbs->addItem('');
            return $this->render('VMFeedbackBundle:Backend:show.html.twig', array( 'feedback' => $feedback));
        }else{
            return $this->redirect($this->generateUrl('bo_feedbacks'));
        }
    }
}

<?php

namespace VM\MakerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AdvertController extends Controller {

    public function isCollaboratorAction() {
       //check if user is collaborator or not redirect to homepage if not collaborator
    }
    
    public function moIndexAction($slug_ent) {
        $request= $this->getRequest();
        $maker = $this->get('maker_repository')->getElements(array('by_slug' => $slug_ent, 'action' => 'one'));

        if($maker){
            $paginate = $this->get("index_paginate");
            $paginate->setH1('Toutes les annonces');
            
            $paginate->setView('VMMakerBundle:Advert/Middle:index.html.twig');
            $paginate->setAddNew('mo_advert_new','Ajouter une formation ',array('advert_type' => 'formation','slug_ent'=>$slug_ent));
            $paginate->setAddNew('mo_advert_new','Ajouter un recrutement ',array('advert_type' => 'recrutement','slug_ent'=>$slug_ent));
            
            $paginate->addQueryParams(array('by_maker_id'=>$maker->getId()));
            $query = $this->get('advert_repository')->getElements($paginate->getParamsForQuery());

            $paginate->setQuery($query);
            return $this->render($paginate->getTemplate(), $paginate->getParams());
        }else{

        }
    }

    public function moShowAction($slug_advert) {
        $advert = $this->get('advert_repository')->getElements(array('by_slug' => $slug_advert, 'action' => 'one'));
        if($advert){
            $breadcrumbs = $this->get("white_october_breadcrumbs");
            $breadcrumbs->addItem("Annonces", $this->get("router")->generate("bo_adverts"));
            $breadcrumbs->addItem('');
            return $this->render('VMMakerBundle:Advert/Middle:show.html.twig', array( 'advert' => $advert));
        }else{
            return $this->redirect($this->generateUrl('mo_adverts',array('slug_ent'=>  $this->getRequest()->get('slug_ent'))));
        }
    }

    public function boIndexAction() {
        $paginate = $this->get("index_paginate");
        $paginate->setH1('Toutes les annonces');
        $paginate->setView('VMMakerBundle:Advert/Back:index.html.twig');
        $paginate->setAddNew('bo_advert_new','Ajouter une formation ',array('advert_type' => 'formation'));
        $paginate->setAddNew('bo_advert_new','Ajouter un recrutement ',array('advert_type' => 'recrutement'));
        $query = $this->get('advert_repository')->getElements($paginate->getParamsForQuery());

        $paginate->setQuery($query);
        return $this->render($paginate->getTemplate(), $paginate->getParams());
    }

    public function boShowAction($slug_advert) {
        $advert = $this->get('advert_repository')->getElements(array('by_slug' => $slug_advert, 'action' => 'one'));
        if($advert){
            $breadcrumbs = $this->get("white_october_breadcrumbs");
            $breadcrumbs->addItem("Annonces", $this->get("router")->generate("bo_adverts"));
            $breadcrumbs->addItem('');
            return $this->render('VMMakerBundle:Advert/Back:show.html.twig', array( 'advert' => $advert));
        }else{
            return $this->redirect($this->generateUrl('bo_adverts'));
        }
    }

    public function boDeleteAction() {
        //if user is admin $this->deleteCollaborator();
    }

    public function moDeleteAction() {
        //if user is collaborator and have higher level $this->deleteCollaborator();
    }

    private function deleteCollaborator() {
        //delete a collaborator by id if exist
    }

}

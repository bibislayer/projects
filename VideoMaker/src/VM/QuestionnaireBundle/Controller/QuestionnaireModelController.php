<?php

namespace VM\QuestionnaireBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class QuestionnaireModelController extends Controller {

    public function indexAction() {
        $client = $this->get('solarium.client');

        // get a select query instance
        $query = $client->createSelect();

        // get the facetset component
        $facetSet = $query->getFacetSet();

        // create a facet field instance and set options
        $facetSet->createFacetField('category')->setField('category_s');
        $facetSet->createFacetField('name')->setField('name');
       
        // this executes the query and returns the result
        $questionnaires = $client->select($query);

        return $this->render('VMQuestionnaireBundle:Middle/Model:index.html.twig', array('questionnaires' => $questionnaires));
    }

    public function searchModelAction() {
        return $this->render('VMQuestionnaireBundle:Default:index.html.twig', array());
    }

}

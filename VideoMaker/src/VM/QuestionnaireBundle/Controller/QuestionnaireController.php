<?php

namespace VM\QuestionnaireBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use VM\QuestionnaireBundle\Filter\QuestionnaireFilterType;
use VM\QuestionnaireBundle\Filter\QuestionnaireUserFilterType;

class QuestionnaireController extends Controller {

    //Backend Listing of questionnaires
    public function boIndexAction() {

        $paginate = $this->get("index_paginate");
        $paginate->setH1('Toutes les questionnaire');
        $paginate->setView('VMQuestionnaireBundle:Back:index.html.twig');

        $paginate->setAddNew('bo_questionnaire_new');
        $paginate->addFilters(new QuestionnaireFilterType(), array('by_enterprise', 'by_type', 'by_status', 'by_has_respondant'));
        $query = $this->get('questionnaire_repository')->getElements($paginate->getParamsForQuery());
        $paginate->setQuery($query);
        return $this->render($paginate->getTemplate(), $paginate->getParams());
    }

    //Middle Office Listing of questionnaires
    public function moIndexAction($slug_ent) {
        $em = $this->getDoctrine()->getManager();
        $enterprise = $em->getRepository('VMEnterpriseBundle:Enterprise')->findOneBy(array('slug' => $slug_ent));

        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem($enterprise->getName(), $this->get("router")->generate("mo_enterprise_show", array('slug_ent' => $slug_ent)));
        $breadcrumbs->addItem('Questionnaires');
        /*
        $paginate = $this->get("index_paginate");
        $paginate->setH1('Questionnaires de l\'entreprise ' . $enterprise->getName());
        $paginate->setView('VMQuestionnaireBundle:Middle:index.html.twig');
        $paginate->setAddNew('mo_questionnaire_new', 'Ajouter un questionnaire', array('slug_ent' => $slug_ent));

        $paginate->addFilters(new QuestionnaireFilterType(), array('by_status', 'by_has_respondant', 'by_type'));
        $paginate->addQueryParams(array('by_ent_id' => $enterprise->getId()));

        $paginate->setQuery($this->get('questionnaire_repository')->getElements($paginate->getParamsForQuery()));

        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem($enterprise->getName(), $this->get("router")->generate("mo_enterprise_show", array('slug_ent' => $slug_ent)));
        $breadcrumbs->addItem('Questionnaires');

        return $this->render($paginate->getTemplate(), $paginate->getParams());
        */

        return $this->render('VMQuestionnaireBundle:Middle:index.html.twig', array('enterprise' => $enterprise));
    }

    //Show questionnaire on Backend
    public function boShowAction() {
        $request = $this->get('request');
        $slug_quest = $request->get('slug_quest');

        $questionnaire = $this->get('questionnaire_repository')->getElements(array('by_slug' => $slug_quest, 'action' => 'one'));
        if ($questionnaire) {
            // BREADCRUMBS
            $breadcrumbs = $this->get("white_october_breadcrumbs");           
            $breadcrumbs->addItem('Questionnaires', $this->get("router")->generate("bo_questionnaires"));
            $breadcrumbs->addItem($questionnaire->getName());

            return $this->render('VMQuestionnaireBundle:Back:show.html.twig', array('questionnaire' => $questionnaire));
        }
    }
    
    //Show questionnaire on Middle Office
    public function moShowAction($slug_quest) {
        $access = $this->get('session')->get('access_admin');
        $enterprise = $access['current'];
        $questionnaire = $this->get('questionnaire_repository')->getElements(array('by_slug' => $slug_quest, 'action' => 'one'));
        if ($questionnaire) {
            // BREADCRUMBS
            $breadcrumbs = $this->get("white_october_breadcrumbs");
            $breadcrumbs->addItem($enterprise['name'], $this->get("router")->generate("mo_enterprise_show", array('slug_ent' => $enterprise['slug'])));
            $breadcrumbs->addItem('Questionnaires', $this->get("router")->generate("mo_questionnaires", array('slug_ent' => $enterprise['slug'])));
            $breadcrumbs->addItem($questionnaire->getName());

            return $this->render('VMQuestionnaireBundle:Middle:show.html.twig', array('questionnaire' => $questionnaire, 'enterprise' => $enterprise));
        }
    }

    //List of respondant user in particular questionnaire on Middle Side
    public function moQuestionnaireRespondantIndexAction($slug_quest) {

        $access = $this->get('session')->get('access_admin');
        $enterprise = $access['current'];
        $questionnaire = $this->get('questionnaire_repository')->getElements(array('action' => 'one', 'by_slug' => $slug_quest));

        if ($questionnaire) {
            $paginate = $this->get("index_paginate");
            $paginate->setH1('Répondants au questionnaire : ' . $questionnaire->getName());
            $paginate->setView('VMQuestionnaireBundle:Middle:user_respondant_index.html.twig');
            $paginate->addFilters(new QuestionnaireUserFilterType($enterprise), array('by_status', 'by_have_score'));
            $paginate->addQueryParams(array('by_questionnaire' => $questionnaire->getId()));
            $paginate->setQuery($this->get('questionnaire_user_repository')->getElements($paginate->getParamsForQuery()));
            // BREADCRUMBS
            $breadcrumbs = $this->get("white_october_breadcrumbs");
            $breadcrumbs->addItem($enterprise['name'], $this->get("router")->generate("mo_enterprise_show", array('slug_ent' => $enterprise['slug'])));
            $breadcrumbs->addItem('Questionnaires', $this->get("router")->generate("mo_questionnaires", array('slug_ent' => $enterprise['slug'])));
            $breadcrumbs->addItem($questionnaire->getName(), $this->get("router")->generate("mo_questionnaire_show", array('slug_ent' => $enterprise['slug'], 'slug_quest' => $questionnaire->getSlug())));
            $breadcrumbs->addItem('Répondants');
            return $this->render($paginate->getTemplate(), $paginate->getParams());
        }
    }

    
    //Making Decision on Middle side
    public function moQuestionnaireRespondantDecisionIndexAction($slug_quest, $questionnaire_user_id) {
        $access = $this->get('session')->get('access_admin');
        $enterprise = $access['current'];
        $questionnaire = $this->get('questionnaire_repository')->getElements(array('action' => 'one', 'by_slug' => $slug_quest));
        $questionnare_user = $this->get('questionnaire_user_repository')->getElements(array('by_questionnaire' => $questionnaire->getId(), 'by_id' => $questionnaire_user_id, 'action' => 'one'));
        if ($questionnare_user->getFirstname() && $questionnare_user->getLastname()) {
            $username = $questionnare_user->getFirstname() . ' ' . $questionnare_user->getLastname();
        } else {
            $username = $questionnare_user->getEmail();
        }
        $administrators = array();
        if ($questionnare_user) {
            if (is_array($questionnare_user->getScore())) {
                foreach ($questionnare_user->getScore() as $key => $score) {
                    if(is_int($key)){
                        echo $key;
                        $administrator = $this->get('user_repository')->getUsers(array('action' => 'one', 'by_id' => $key));
                        $administrators[$administrator->getId()] = array('score' => $score, 'administrator' => $administrator);
                    }
                }
            }
            if (is_array($questionnare_user->getComments())) {
                foreach ($questionnare_user->getComments() as $key => $comment) {
                    $administrators[$key]['comment'] = $comment;
                }
            }
            // BREADCRUMBS
            $breadcrumbs = $this->get("white_october_breadcrumbs");
            $breadcrumbs->addItem($enterprise['name'], $this->get("router")->generate("mo_enterprise_show", array('slug_ent' => $enterprise['slug'])));
            $breadcrumbs->addItem('Questionnaires', $this->get("router")->generate("mo_questionnaires", array('slug_ent' => $enterprise['slug'])));
            $breadcrumbs->addItem($questionnaire->getName(), $this->get("router")->generate("mo_questionnaire_show", array('slug_ent' => $enterprise['slug'], 'slug_quest' => $questionnaire->getSlug())));
            $breadcrumbs->addItem($username, $this->get("router")->generate("mo_questionnaire_user_respondant_show", array('slug_ent' => $enterprise['slug'], 'slug_quest' => $questionnaire->getSlug(), 'respondant_id' => $questionnaire_user_id)));
            $breadcrumbs->addItem('Répondants decision');

            return $this->render('VMQuestionnaireBundle:Middle:user_respondant_decision_index.html.twig', array('questionnare_user' => $questionnare_user, 'administrators' => $administrators));
        }
    }
    
    //List of respondant user in particular questionnaire on Backend Side
    public function boQuestionnaireRespondantIndexAction($slug_quest = NULL) {

        $questionnaire = $this->get('questionnaire_repository')->getElements(array('action' => 'one', 'by_slug' => $slug_quest));

        if($questionnaire || !$slug_quest){
            $paginate = $this->get("index_paginate");
            $paginate->setH1('Toutes les questionnaire respondant');
            $paginate->setView('VMQuestionnaireBundle:Back:user_respondant_index.html.twig');
            $paginate->addFilters(new QuestionnaireUserFilterType(), array('by_status', 'by_have_score'));
            if($questionnaire){
                $paginate->addQueryParams(array('by_questionnaire' => $questionnaire->getId()));
            }
            $query = $this->get('questionnaire_user_repository')->getElements($paginate->getParamsForQuery());
            $paginate->setQuery($query);
            return $this->render($paginate->getTemplate(), $paginate->getParams());
        }
    }
    
    
    //Show respondant user  result in particular questionnaire on Middle Side   
    
    public function moRespondantShowAction($slug_quest, $respondant_id) {

        $access = $this->get('session')->get('access_admin');
        $enterprise = $access['current'];
        $questionnaire = $this->get('questionnaire_repository')->getElements(array('action' => 'one', 'by_slug' => $slug_quest));

        $questions_respondant = $this->get('question_repository')->getElements(array('action' => 'execute', 'by_respondant_id' => $respondant_id, 'by_quest_id' => $questionnaire->getId(), 'order_by' => array('field' => 'qe.position')));

        // BREADCRUMBS
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem($enterprise['name'], $this->get("router")->generate("mo_enterprise_show", array('slug_ent' => $enterprise['slug'])));
        $breadcrumbs->addItem('Questionnaires', $this->get("router")->generate("mo_questionnaires", array('slug_ent' => $enterprise['slug'])));
        $breadcrumbs->addItem($questionnaire->getName(), $this->get("router")->generate("mo_questionnaire_show", array('slug_ent' => $enterprise['slug'], 'slug_quest' => $questionnaire->getSlug())));

        //If user attempt quetsionnaire and give response also    
        if ($questionnaire && $questions_respondant) {
            $response = $questions_respondant[0]->getQuestionnaireQuestionResponse();

            $respondant = '';
            foreach ($response as $resp) {
                //checking is response questionanire user is same as responde user
                if ($respondant_id == $resp->getQuestionnaireUser()->getId() && $respondant == '') {
                    $respondant = $resp->getQuestionnaireUser();
                }
            }

            $breadcrumbs->addItem('Répondants', $this->get("router")->generate("mo_questionnaire_user_respondants", array('slug_ent' => $enterprise['slug'], 'slug_quest' => $questionnaire->getSlug())));
            $breadcrumbs->addItem($respondant->getFirstname() . ' ' . $respondant->getLastname());
            return $this->render('VMQuestionnaireBundle:Middle:respondant_show.html.twig', array('questionnaire' => $questionnaire, 'questions_respondant' => $questions_respondant, 'respondant' => $respondant, 'enterprise' => $enterprise));
        } else {
            //if user attempt for this questionaire
            $respondant = $this->get('questionnaire_user_repository')->getElements(array('action' => 'one', 'by_id' => $respondant_id, 'by_questionnaire' => $questionnaire->getId()));

            if ($respondant) {
                $breadcrumbs->addItem('Répondants', $this->get("router")->generate("mo_questionnaire_user_respondants", array('slug_ent' => $enterprise['slug'], 'slug_quest' => $questionnaire->getSlug())));
                $breadcrumbs->addItem($respondant->getFirstname() . ' ' . $respondant->getLastname());
                return $this->render('VMQuestionnaireBundle:Middle:respondant_show.html.twig', array('questionnaire' => $questionnaire, 'respondant' => $respondant, 'enterprise' => $enterprise));
            } else {
                return $this->redirect($this->generateUrl('mo_questionnaire_user_respondants', array('slug_ent' => $this->get('request')->get('slug_ent'), 'slug_quest' => $slug_quest)));
            }
        }
    }

     //Show respondant user  result in particular questionnaire on Backend Side
    public function boRespondantShowAction($slug_quest, $respondant_id) {

        $enterprise = array();
        $questionnaire = $this->get('questionnaire_repository')->getElements(array('action' => 'one', 'by_slug' => $slug_quest));
        $questions_respondant = $this->get('question_repository')->getElements(array('action' => 'execute', 'by_respondant_id' => $respondant_id, 'by_quest_id' => $questionnaire->getId()));

        // BREADCRUMBS
        $breadcrumbs = $this->get("white_october_breadcrumbs");        
        $breadcrumbs->addItem('Questionnaires', $this->get("router")->generate("bo_questionnaires"));
        $breadcrumbs->addItem($questionnaire->getName(), $this->get("router")->generate("bo_questionnaire_show", array('slug_quest' => $questionnaire->getSlug())));

        //If user attempt quetsionnaire and give response also    
        if ($questionnaire && $questions_respondant) {
            $response = $questions_respondant[0]->getQuestionnaireQuestionResponse();

            $respondant = '';
            foreach ($response as $resp) {
                //checking is response questionanire user is same as responde user
                if ($respondant_id == $resp->getQuestionnaireUser()->getId() && $respondant == '') {
                    $respondant = $resp->getQuestionnaireUser();
                }
            }

            $breadcrumbs->addItem('Répondants', $this->get("router")->generate("bo_questionnaire_user_respondants", array('slug_quest' => $questionnaire->getSlug())));
            $breadcrumbs->addItem($respondant->getFirstname() . ' ' . $respondant->getLastname());
            return $this->render('VMQuestionnaireBundle:Back:respondant_show.html.twig', array('questionnaire' => $questionnaire, 'questions_respondant' => $questions_respondant, 'respondant' => $respondant, 'enterprise' => $enterprise));
        } else {
            //if user attempt for this questionaire
            $respondant = $this->get('questionnaire_user_repository')->getElements(array('action' => 'one', 'by_id' => $respondant_id, 'by_quest_id' => $questionnaire->getId()));

            if ($respondant) {
                $breadcrumbs->addItem('Répondants', $this->get("router")->generate("mo_questionnaire_user_respondants", array('slug_quest' => $questionnaire->getSlug())));
                $breadcrumbs->addItem($respondant->getFirstname() . ' ' . $respondant->getLastname());
                return $this->render('VMQuestionnaireBundle:Back:respondant_show.html.twig', array('questionnaire' => $questionnaire, 'respondant' => $respondant, 'enterprise' => $enterprise));
            } else {
                return $this->redirect($this->generateUrl('bo_questionnaire_user_respondants', array('slug_quest' => $slug_quest)));
            }
        }
    }

    //Making decision on Backend side
    public function boQuestionnaireRespondantDecisionIndexAction($slug_quest, $questionnaire_user_id) {
        
        $questionnaire = $this->get('questionnaire_repository')->getElements(array('action' => 'one', 'by_slug' => $slug_quest));
        $questionnare_user = $this->get('questionnaire_user_repository')->getElements(array('by_questionnaire' => $questionnaire->getId(), 'by_id' => $questionnaire_user_id, 'action' => 'one'));
        if ($questionnare_user->getFirstname() && $questionnare_user->getLastname()) {
            $username = $questionnare_user->getFirstname() . ' ' . $questionnare_user->getLastname();
        } else {
            $username = $questionnare_user->getEmail();
        }
        $administrators = array();
        if ($questionnare_user) {
            if (is_array($questionnare_user->getScore())) {
                foreach ($questionnare_user->getScore() as $key => $score) {
                    $administrator = $this->get('user_repository')->getUsers(array('action' => 'one', 'by_id' => $key));
                    $administrators[($administrator->getUserProfile() ? $administrator->getUserProfile()->getFirstName() . ' ' . $administrator->getUserProfile()->getLastName() : $administrator->getEmail())] = $score;
                }
            }
            // BREADCRUMBS
            $breadcrumbs = $this->get("white_october_breadcrumbs");
            $breadcrumbs->addItem('Questionnaires', $this->get("router")->generate("bo_questionnaires"));
            $breadcrumbs->addItem($questionnaire->getName(), $this->get("router")->generate("bo_questionnaire_show", array('slug_quest' => $questionnaire->getSlug())));
            $breadcrumbs->addItem($username, $this->get("router")->generate("bo_questionnaire_user_respondant_show", array('slug_quest' => $questionnaire->getSlug(), 'respondant_id' => $questionnaire_user_id)));
            $breadcrumbs->addItem('Répondants decision');

            return $this->render('VMQuestionnaireBundle:Back:user_respondant_decision_index.html.twig', array('questionnare_user' => $questionnare_user, 'administrators' => $administrators));
        }
    }

    public function moQuestionnaireProcessAction() {
        return $this->render('VMQuestionnaireBundle:Front:questionnaire_process.html.twig', array());
    }

    public function moQuestionnaireSuccessAction() {
        return $this->render('VMQuestionnaireBundle:Front:questionnaire_success.html.twig', array());
    }

    private function getNextElement() {
        //return next element by param
    }

    //for setting status
    public function statusAction() {
        $request = $this->get('request');
        $total = 0;
        //Getting environment for backend, middle or front
        $env = substr($request->get('_route'), 0, 3);
        $enterprise = $this->get('enterprise_repository')->getElements(array('action' => 'one', 'by_slug' => $request->get('slug_ent')));
        
        $lastcredit = $this->get('credits_history_repository')->getElements(array('by_enterprise_id' => $enterprise->getId(), 'action' => 'one', 'limit' => 1, 'order_by' => array('field' => 'created_at', 'sort' => 'DESC')));
        if($lastcredit)
            $total = $lastcredit->getTotal();
        if ($total == 0 && $request->get('status') != 'unpublished') {
            $url = $this->generateUrl("mo_credit_histories", array("slug_ent" => $request->get('slug_ent')));
            $request->getSession()->getFlashBag()->set('error', 'Vous n\'avez pas assez de crédit pour publier ce questionnaire. Vous pouvez ajouter du crédit en suivant ce lien => <a href="' . $url . '">Gestion credit</a>');
        } else {
            if ($request->get('status') == 'published') {
                $this->get('credits_history_controler')->moAddCreditAction($request->get('slug_ent'), 'debit', 1, $request->get('slug_quest'));
            }
            //changing status of questionnaire 
            $this->get('questionnaire_repository')->changeStatus(array('by_type' => $request->get('status'), 'slug' => $request->get('slug_quest')));
        }
        if ($this->getRequest()->headers->get('referer')) {
            return $this->redirect($this->getRequest()->headers->get('referer'));
        } else {
            if ($env == 'mo_') {
                return $this->redirect($this->generateUrl('mo_questionnaire_show', array('slug_ent' => $request->get('slug_ent'), 'slug_quest' => $request->get('slug_quest'))));
            } else {
                
            }
        }
    }

    /*     * *
     * This is a component to display Questionnaires in list by parameters
     * @param array $params
     * @return \Symfony\Bundle\FrameworkBundle\Controller\Response
     */
    public function questionnaireListAction($params = array()) {

        $list = $this->get("list_model");

        $baseArray = array(
            'paramsQ' => array('order_by' => array('field' => 'name', 'sort' => 'asc'))
        );

        if($list->getEnv() == 'bo'){
            $baseArray['template'] = 'VMQuestionnaireBundle:Back:_questionnaires.html.twig';
        }else{
            $baseArray['template'] = 'VMQuestionnaireBundle:Middle:_list_questionnaires.html.twig';
        }

        $list->defineBase($baseArray);
        $list->passedParams($params);
        $list->setQuery($this->get('questionnaire_repository')->getElements($list->getQueryParams()));
        return $list->getReturn();

    }
}

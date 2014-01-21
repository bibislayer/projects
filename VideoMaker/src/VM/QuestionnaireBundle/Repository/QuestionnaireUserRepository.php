<?php

namespace VM\QuestionnaireBundle\Repository;
use VM\GeneralBundle\Model\RepositoryModel;
use Doctrine\ORM\EntityRepository;

class QuestionnaireUserRepository extends EntityRepository {
    
     ################### Optimize Lib Use Of Generic Function ################################
      public function getElements($params = array()){

        $q = $this->createQueryBuilder('qu');
        $m = new RepositoryModel($q, $params);
        $m->changeBase(array('name'=>'qu.email'));
        $this->getFieldsForQuery($q, $params);

        $this->getByParamsForQuery($q, $params);
        return $m->finalize();
    }
    
    
    /**
     * Get the fields action for the Query Select.
     * @return object AssociationTable
     */
    private function getFieldsForQuery($q, $params) {

        $q->innerJoin('qu.Questionnaire', 'q');
        $q->leftJoin('qu.User', 'u');
        $q->leftJoin('u.UserProfile', 'p');
        $q->leftJoin('q.Enterprise', 'e');
        //$q->leftJoin('qu.QuestionnaireQuestionResponse', 'qqr');
        
        // Get Level Of Length

        /*$model = New RepositoryModel($q);
        $length = $q->getLength();

        // Get Tiny Data
        if ($length >= 1) {

            // Get Small Data 
            if ($length >= 2) {
                    
                // Get Extend Data
                if ($length >= 3) {

                    // Get Complete Data
                    if ($length >= 4) {
                        
                    }
                }
            }
        }*/

       
        // Return Bind Fields Object 
        return $q;
    }
    
    /**
     * Get the fields Filter Action for Data.
     * @return object QuestionnaireTable
     */
    private function getByParamsForQuery($q, $params = array()) {

        // Get By Enterprise Id
        if (array_key_exists('by_ent_id', $params)) {            
            $q->andWhere($q->expr()->eq('e.id', $params['by_ent_id']));
        }
         if (array_key_exists('by_id', $params)) {            
            $q->andWhere($q->expr()->eq('qu.id', $params['by_id']));
        }
        // Get By Questionnaire Id
        if (array_key_exists('by_questionnaire', $params)) {
            $q->andWhere($q->expr()->eq('qu.Questionnaire', $params['by_questionnaire']));
        }
        
        if (array_key_exists('by_user_id', $params)) {
            $q->andWhere($q->expr()->eq('qu.User', $params['by_user_id']));
        }
        if (array_key_exists('by_email_user', $params)) {
            $q->andWhere($q->expr()->eq('qu.email', "'".$params['by_email_user']."'"));
        }
        // Get By Status
        if (array_key_exists('by_status', $params)) {            
            if($params['by_status'] == 'accepted'){
                $q->andWhere($q->expr()->eq('qu.status', 1));                
            }elseif($params['by_status'] == 'refused'){
                $q->andWhere($q->expr()->eq('qu.status', 2));                
            }elseif($params['by_status'] == 'test'){
                $q->andWhere($q->expr()->eq('qu.status', 3));
            }elseif($params['by_status'] == 'waiting'){
                $q->andWhere('(qu.status = 0 OR qu.status IS NULL)');
            }
        }
        // Get By Have Score
        if (array_key_exists('by_have_score', $params)) {            
            if($params['by_have_score'] == 'non_scored'){
                $q->andWhere($q->expr()->isNull('qu.score'));                
            }elseif($params['by_have_score'] == 'scored'){
                $q->andWhere($q->expr()->isNotNull('qu.score'));                
            }elseif($params['by_have_score'] == 'scored_array'){
                $q->andWhere($q->expr()->like('qu.score','\'a:%\''));                
            }
        }
        return $q;
    }
}

?>
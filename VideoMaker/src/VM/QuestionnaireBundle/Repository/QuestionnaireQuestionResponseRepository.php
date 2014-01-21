<?php

namespace VM\QuestionnaireBundle\Repository;
use VM\GeneralBundle\Model\RepositoryModel;
use Doctrine\ORM\EntityRepository;

class QuestionnaireQuestionResponseRepository extends EntityRepository {
    
    public function getElements($params = array()){

        $q = $this->createQueryBuilder('qr');

        $m = new RepositoryModel($q, $params);

        $this->getFieldsForQuery($q, $params);

        $this->getByParamsForQuery($q, $params);

        return $m->finalize();
    }
    
    
    /**
     * Get the fields action for the Query Select.
     * @return object AssociationTable
     */
    private function getFieldsForQuery($q, $params) {
        
        if(array_key_exists('action', $params) && $params['action']=='count'){
            $q->select('count(qr)');
        }
        
        $q->leftJoin('qr.Question', 'q');
        $q->leftJoin('qr.QuestionnaireUser', 'qu');
        // Get Level Of Length
        $model = New RepositoryModel($q);
        $length = $model->getLength();

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
        }

       
        // Return Bind Fields Object 
        return $q;
    }
    
    /**
     * Get the fields Filter Action for Data.
     * @return object QuestionnaireTable
     */
    private function getByParamsForQuery($q, $params = array()) {
        if (array_key_exists('by_id', $params)) {
            $q->andWhere($q->expr()->eq('qr.id', $params['by_id']));
        }
         if (array_key_exists('by_question_id', $params)) {
            $q->andWhere($q->expr()->eq('q.id', $params['by_question_id']));
        }
         if (array_key_exists('by_repondant_id', $params)) {
            $q->andWhere($q->expr()->eq('qr.QuestionnaireUser', $params['by_repondant_id']));
        }
        return $q;

    }
    
    public function removeElements($params = array()){
        $q = $this->createQueryBuilder('qr')                ->delete();
        
        $this->getByParamsForQuery($q, $params);

        return $q->getQuery()->getResult();
    }
}

?>
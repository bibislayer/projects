<?php

namespace VM\QuestionnaireBundle\Repository;
use VM\GeneralBundle\Model\RepositoryModel;
use Doctrine\ORM\EntityRepository;

class QuestionRepository extends EntityRepository {
    
    public function getElements($params = array()){

        $q = $this->createQueryBuilder('q');

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
        // Get Level Of Length
        $q->leftJoin('q.QuestionnaireQuestionResponse', 'qr');
        $q->leftJoin('qr.QuestionnaireQuestionChoice', 'qc');
        $q->leftJoin('q.QuestionnaireElement', 'qe');
        $q->leftJoin('qr.QuestionnaireUser', 'qu');

        /*$model = New RepositoryModel($q);
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
        }*/

       
        // Return Bind Fields Object 
        return $q;
    }
    
    /**
     * Get the fields Filter Action for Data.
     * @return object QuestionnaireTable
     */
    private function getByParamsForQuery($q, $params = array()) {
        if (array_key_exists('by_respondant_id', $params)) {
            $q->andWhere($q->expr()->eq('qu.id', $params['by_respondant_id']));
        }
        if (array_key_exists('by_quest_id', $params)) {
          $q->andWhere($q->expr()->eq('qu.Questionnaire', $params['by_quest_id']));
        }
        
        return $q;

    }
}

?>
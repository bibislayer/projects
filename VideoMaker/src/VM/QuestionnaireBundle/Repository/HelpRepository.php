<?php

namespace VM\QuestionnaireBundle\Repository;

use VM\GeneralBundle\Model\RepositoryModel;
use Doctrine\ORM\EntityRepository;

class HelpRepository extends EntityRepository {
    
     ################### Optimize Lib Use Of Generic Function ################################
      public function getElements($params = array()){

        $q = $this->createQueryBuilder('h');

        $m = new RepositoryModel($q, $params);
        
        $q = $m->getQ();

        $this->getFieldsForQuery($q, $params);

        $this->getByParamsForQuery($q, $params);

        return $m->finalize();
    }
    
    
    /**
     * Get the fields action for the Query Select.
     * @return object HelpTable
     */
    private function getFieldsForQuery($q, $params) {
        // Get Level Of Length

        $model = New RepositoryModel($q);
        $length = $model->getLength();
        
        $q->leftJoin('h.Questionnaire', 'qe')
            ->leftJoin('qe.StdQuestionnaireType','sqet');
        $q->leftJoin('h.Question','q')
            ->leftJoin('q.StdQuestionType','sqt');

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
     * @return object HelpTable
     */
    private function getByParamsForQuery($q, $params = array()) {
        
        // Filter by id
        if(array_key_exists('by_id', $params) && $params['by_id']!=''){
            $q->andWhere($q->expr()->eq('h.id',$params['by_id']));
        }   
        
        return $q;

    }
}

?>
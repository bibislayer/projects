<?php

namespace VM\QuestionnaireBundle\Repository;
use VM\GeneralBundle\Model\RepositoryModel;
use Doctrine\ORM\EntityRepository;

class QuestionnaireQuestionChoiceRepository extends EntityRepository {
    
     ################### Optimize Lib Use Of Generic Function ################################
      public function getElements($params){

        $q = $this->createQueryBuilder('qqc');

        $m = new RepositoryModel($q, $params);
        
        $q = $m->getQ();

        $this->getFieldsForQuery($q, $params);

        $this->getByParamsForQuery($q, $params);

        return $m->finalize();
    }
    
    
    /**
     * Get the fields action for the Query Select.
     * @return object QuestionnaireQuestionChoiceTable
     */
    private function getFieldsForQuery($q, $params) {
        // Get Level Of Length

        if(array_key_exists('max_count', $params)){
            $q->addSelect('Max(qqc.'.$params['max_count'].') as max_value');
        }
        
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
     * @return object QuestionnaireQuestionChoiceTable
     */
    private function getByParamsForQuery($q, $params = array()) {       
        return $q;

    }
}

<?php

namespace VM\VideoBundle\Repository;
use VM\GeneralBundle\Model\RepositoryModel;
use Doctrine\ORM\EntityRepository;

class VideoRepository extends EntityRepository {
    
     ################### Optimize Lib Use Of Generic Function ################################
      public function getElements($params){

        $q = $this->createQueryBuilder('v');

        $m = new RepositoryModel($q, $params);
        //$m->changeBase(array('name' => 'en.name'));
        $q = $m->getQ();

        $this->getFieldsForQuery($q, $params);

        $this->getByParamsForQuery($q, $params);

        return $m->finalize();
    }
    
    
    /**
     * Get the fields action for the Query Select.
     * @return object QuestionnaireElementTable
     */
    private function getFieldsForQuery($q, $params) {
        // Get Level Of Length

        if(array_key_exists('max_count', $params)){
            $q->addSelect('Max(qu.'.$params['max_count'].') as max_value');
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
     * @return object QuestionnaireElementTable
     */
    private function getByParamsForQuery($q, $params = array()) {
        
        if (array_key_exists('by_level', $params)){
            $q->andWhere($q->expr()->eq('qu.level', "'".$params['by_level']."'"));
        }
        
        if (array_key_exists('by_position', $params)){
            $q->andWhere($q->expr()->eq('qu.position', "'".$params['by_position']."'"));
        }
        
        if (array_key_exists('by_questionnaire_id', $params) && $params['by_questionnaire_id']!='' ){
            $q->andWhere($q->expr()->eq('qu.Questionnaire', $params['by_questionnaire_id']));
        }
        if (array_key_exists('by_id', $params) && $params['by_id']!='' ){
            $q->andWhere($q->expr()->eq('qu.id', $params['by_id']));
        }
        
        return $q;

    }
}

?>
<?php

namespace Poker\PokerTableBundle\Repository;
use Poker\GeneralBundle\Model\RepositoryModel;
use Doctrine\ORM\EntityRepository;

class PokerUserRepository extends EntityRepository {
    
     ################### Optimize Lib Use Of Generic Function ################################
      public function getElements($params = array()){

        $q = $this->createQueryBuilder('pu');

        $m = new RepositoryModel($q, $params);
        //$m->changeBase(array('name' => 'en.name'));
        $q = $m->getQ();

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
        $q->leftJoin('pu.PokerTable', 'pt');
        $q->leftJoin('pu.User', 'u');
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
     * @return object RecordingSessionTable
     */
    private function getByParamsForQuery($q, $params = array()) {
        
        // Get By Enterprise Id
        if (array_key_exists('by_user_id', $params)) {            
            $q->andWhere($q->expr()->eq('pu.User', $params['by_user_id']));
        }
        if (array_key_exists('by_place', $params)) {            
            $q->andWhere($q->expr()->eq('pu.place', $params['by_place']));
        }
        // Get By Enterprise Id
        if (array_key_exists('by_table_id', $params)) {            
            $q->andWhere($q->expr()->eq('pu.PokerTable', $params['by_table_id']));
        }
        
        return $q;
    }
}

?>

<?php

namespace VM\StandardBundle\Repository;

use VM\GeneralBundle\Model\RepositoryModel;
use Doctrine\ORM\EntityRepository;

class StdQuestionnaireTypeElementRepository extends EntityRepository {
    
    ################### Optimize Lib Use Of Generic Function ################################

    /*****
    * Base function to get all the datas for getting EnterpriseInvitationAdministrator
    * @param array $params
    * @return Doctrine_Query
    */
    public function getElements($params=array()){
        
        $q = $this->createQueryBuilder('sqte');

        $m = new RepositoryModel($q, $params);
        
        $q = $m->getQ();

        $this->getFieldsForQuery($q, $params);

        $this->getByParamsForQuery($q, $params);
        
        return $m->finalize();
    }
    
    /**
    * Get the fields action for the Query Select.
    * @return object EnterpriseInvitationAdministrator
    */
    private function getFieldsForQuery($q, $params){

        $model = New RepositoryModel($q);
        $length = $model->getLength();
               
        // Get Tiny Data 
        if ($length>=1) { 

            // Get Small Data   
            if ($length>=2) {

                // Get Extend Data
                if ($length>=3) {

                    // Get Complete Data
                    if ($length>=4) {

                    }
                }
            }
        }

        // Get Extra Select
        if (array_key_exists('extra_select', $params) && is_array($params['extra_select'])) {
            
            // Get Count UserCart By Params
            if (array_key_exists('count', $params['extra_select']) && is_array($params['extra_select']['count'])) {
                
            }
        }

        // Return Bind Fields Object 
        return $q;
    }

    /**
     * Get the fields Filter Action for Data.
     * @return object EnterpriseInvitationAdministrator
     */
    private function getByParamsForQuery($q, $params = array()){

        // Return Object After Set Filters
        return $q;
    }
}

?>
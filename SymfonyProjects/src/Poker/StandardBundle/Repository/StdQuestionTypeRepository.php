<?php

namespace Poker\StandardBundle\Repository;

use Poker\GeneralBundle\Model\RepositoryModel;
use Doctrine\ORM\EntityRepository;
/**
 * Description of StandardModel
 *
 * @author thevenet
 */
class StdQuestionTypeRepository extends EntityRepository {

    public function getElements($params){

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
        
        // Filter by id
        if(array_key_exists('by_id', $params) && $params['by_id']!=''){
            $q->andWhere($q->expr()->eq('sqte.id',$params['by_id']));
        }

        if (array_key_exists('by_template', $params) && $params['by_template']){
            $q->andWhere($q->expr()->eq('sqte.template', "'".$params['by_template']."'"));
        }
        
        if (array_key_exists('by_is_active', $params) && $params['by_is_active']){
            $q->andWhere($q->expr()->eq('sqte.is_active', "'".$params['by_is_active']."'"));
        }
        // Return Object After Set Filters
        return $q;
    }
}
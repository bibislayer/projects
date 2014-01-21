<?php

namespace VM\MakerBundle\Repository;

use VM\GeneralBundle\Model\RepositoryModel;
use Doctrine\ORM\EntityRepository;

class MakerInvitationAdministratorRepository extends EntityRepository {
    
    ################### Optimize Lib Use Of Generic Function ################################

    /*****
    * Base function to get all the datas for getting MakerInvitationAdministrator
    * @param array $params
    * @return Doctrine_Query
    */
    public function getElements($params=array()){
        
        $q = $this->createQueryBuilder('eia');

        $m = new RepositoryModel($q, $params);
        
        //$q = $m->getQ();

        $this->getFieldsForQuery($q, $params);

        $this->getByParamsForQuery($q, $params);
        
        return $m->finalize();
    }
    
    /**
    * Get the fields action for the Query Select.
    * @return object MakerInvitationAdministrator
    */
    private function getFieldsForQuery($q, $params){

        $model = New RepositoryModel($q);
        $length = $model->getLength();
        
        $q->leftJoin('eia.Maker','en');
        $q->leftJoin('eia.User','u');
               
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
     * @return object MakerInvitationAdministrator
     */
    private function getByParamsForQuery($q, $params = array()){
        
        // Filter By Id
        if (array_key_exists('by_id', $params)) {
            $q->where($q->expr()->eq('eia.id',$params['by_id']));
        }
        // Filter By Maker Id
        if (array_key_exists('by_maker_id', $params)) {
            $q->andWhere($q->expr()->eq('en.id',$params['by_maker_id']));
        }
        // Filter By User Id
        if (array_key_exists('by_user_id', $params)) {
            $q->andWhere($q->expr()->eq('u.id',$params['by_user_id']));
        }

        // Filter By User Id
        if (array_key_exists('by_email', $params)) {
            $q->andWhere($q->expr()->eq('eia.email', "'".$params['by_email']."'"));
        }
        // Filter By Confirmation Token
        if (array_key_exists('by_confirm_token', $params) && $params['by_confirm_token']!='') {
            $q->andWhere($q->expr()->eq('eia.confirmation_token', "'".$params['by_confirm_token']."'"));
        }
        if(array_key_exists('ent_id_in', $params)){
            $q->andWhere($q->expr()->in('eia.id', $params['ent_id_in']));
        }

        // Return Object After Set Filters
        return $q;
    }
}

?>
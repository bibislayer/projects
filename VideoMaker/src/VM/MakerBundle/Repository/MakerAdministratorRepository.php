<?php

namespace VM\MakerBundle\Repository;

use VM\GeneralBundle\Model\RepositoryModel;
use Doctrine\ORM\EntityRepository;

class MakerAdministratorRepository extends EntityRepository {
    
    public function isCollaborator($entreprise_id, $user_id) {
        
         $q = $this->createQueryBuilder('c')
                ->leftJoin('c.Maker', 'en')
                ->leftJoin('c.User', 'u');
                $q->where($q->expr()->eq('c.Maker', $entreprise_id))
                ->andWhere($q->expr()->eq('c.User', $user_id));
             
         $m = new RepositoryModel($q, array('action'=>'one'));         
         $result= $m->finalize();    
         if($result)
             return true;
         else
             return false; 
    }
    public function isCollaboratorSlug($entreprise_slug, $user_id) {

         $q = $this->createQueryBuilder('c')
                ->select('c.id')
                ->leftJoin('c.Maker', 'en')
                ->leftJoin('c.User', 'u');

             //$q->where($q->expr()->eq('en.slug', '"'.$entreprise_id.'"'))
             $q->where($q->expr()->eq('en.slug', "'".$entreprise_slug."'"))
                ->andWhere($q->expr()->eq('u.id', $user_id));
             
         $m = new RepositoryModel($q, array('actions'=>'one'));         
         $result = $m->finalize();    
         
         if($result)
             return true;
         else
             return false;
    }
    
    ################### Optimize Lib Use Of Generic Function ################################

    /*****
    * Base function to get all the datas for getting MakerAdministrator
    * @param array $params
    * @return Doctrine_Query
    */
    public function getElements($params=array()){
        
        $q = $this->createQueryBuilder('c');

        $m = new RepositoryModel($q, $params);
        
        //$q = $m->getQ();

        $this->getFieldsForQuery($q, $params);

        $this->getByParamsForQuery($q, $params);
        
        return $m->finalize();
    }
    
    /**
    * Get the fields action for the Query Select.
    * @return object MakerAdministrator
    */
    private function getFieldsForQuery($q, $params){

        $model = New RepositoryModel($q);
        $length = $model->getLength();
        
        $q->leftJoin('c.Maker','en');
        $q->leftJoin('c.User','u');
               
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
     * @return object MakerAdministrator
     */
    private function getByParamsForQuery($q, $params = array()){
        
        // Filter By Id
        if (array_key_exists('by_id', $params)) {
            $q->where($q->expr()->eq('c.id',$params['by_id']));
        }
        // Filter By Maker Id
        if (array_key_exists('by_maker_id', $params)) {
            $q->andWhere($q->expr()->eq('en.id',$params['by_maker_id']));
        }
        // Filter By User Id
        if (array_key_exists('by_user_id', $params)) {
            $q->andWhere($q->expr()->eq('u.id',$params['by_user_id']));
        }
        // Filter By Maker slug
        if (array_key_exists('by_maker_slug', $params)) {
            $q->andWhere($q->expr()->eq('en.slug',"'".$params['by_maker_slug']."'"));
        }
        // Filter By Roles
        if (array_key_exists('by_role', $params)) {
            
            if($params['by_role']=='ROLE_SALER'){
                $q->andWhere($q->expr()->like('c.roles','\'%"ROLE_SALER"%\''));
            }else if($params['by_role']=='ROLE_COLLAB'){
                $q->andWhere($q->expr()->not($q->expr()->like('c.roles','\'%"ROLE_SALER"%\'')));
            }
        }

        // Return Object After Set Filters
        return $q;
    }
}

?>
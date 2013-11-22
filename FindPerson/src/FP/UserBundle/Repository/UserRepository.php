<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


namespace FP\UserBundle\Repository;

use FP\GeneralBundle\Model\RepositoryModel;
use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository  {
    
     public function getUsers($params = array()) {    
        $q = $this->createQueryBuilder('u');

        $m = new RepositoryModel($q, $params);
        
        $q = $m->getQ();

        $this->getFieldsForQuery($q, $params);

        $this->getByParamsForQuery($q, $params);

        return $m->finalize();
        
    }
    
    /**
     * Get the fields action for the Query Select.
     *
     * @return object UserTable
     */
    /**
     * Get the fields action for the Query Select.
     *
     * @return object UserTable
     */
    private function getFieldsForQuery($q, $params = array()) {

        $model = New RepositoryModel($q);
        $length = $model->getLength();


        if ($length >= 1) {
            // Get some extra field of user
            
            if ($length >= 2) {
                // Get the services of the user
                
                if ($length >= 3) {
                    // Get precise informations on the user
                    if ($length >= 4) {
                        if ($length >= 5) {
                            
                        }
                    }
                }
            }
        }
        
        return $q; 
   }
   
   

   /**
     * Get the fields action for the Query Select.
     *
     * @return object UserTable
     */
    private function getByParamsForQuery($q, $params = array()) {
       //
       
        //Get by user id
        if (array_key_exists('by_id', $params)) {
             $q->where($q->expr()->eq('u.id',$params['by_id']));
        }
        //Get by Email
        if (array_key_exists('by_email', $params)) {
             $q->andWhere($q->expr()->eq('u.email',"'".$params['by_email']."'"));
        }              
          
        return $q;
    }
    
}

?>

<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


namespace SO\UserBundle\Repository;

use SO\StandardBundle\Repository\StandardRepository;

class UserRepository extends StandardRepository  {
    
     public function getUsers($params = array()) {    
      
        $q = $this->createQueryBuilder('u');

        $this->getFieldsForQuery($q, $params);

        $this->getByParamsForQuery($q, $params);

        $this->getLimitForQuery($q, $params);
        
        return $this->getActionsForQuery($q, $params);
        
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

        $length = $this->getLengthForQuery($q, $params);



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
       
       
        //Get by user id
        if (array_key_exists('by_id', $params)) {
             $q->andwhere($q->expr()->eq('u.id',$params['by_id']));
        }              
          
        return $q;
    }
    
}

?>

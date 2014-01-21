<?php

namespace VM\FeedbackBundle\Repository;

use VM\GeneralBundle\Model\RepositoryModel;
use Doctrine\ORM\EntityRepository;

class FeedbackRepository extends EntityRepository {
    
    ################### Optimize Lib Use Of Generic Function ################################

    /*****
    * Base function to get all the datas for getting Feedback
    * @param array $params
    * @return Doctrine_Query
    */

    public function getElements($params){

        $q = $this->createQueryBuilder('fd');

        $m = new RepositoryModel($q, $params);
        
        $q = $m->getQ();

        $this->getFieldsForQuery($q, $params);

        $this->getByParamsForQuery($q, $params);

        return $m->finalize();
    }
    
    /**
    * Get the fields action for the Query Select.
    * @return object Feedback
    */
    private function getFieldsForQuery($q, $params){

        // Get Length of Level
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
     * @return object Feedback
     */
    private function getByParamsForQuery($q, $params = array()){
        
        // Filter By Id
        if (array_key_exists('by_id', $params)) {
            $q->where($q->expr()->eq('fd.id',$params['by_id']));
        }
        if(array_key_exists('fd_id_in', $params)){
            $q->andWhere($q->expr()->in('fd.id',$params['fd_id_in']));
        }


        // Return Object After Set Filters
        return $q;
    }
    /**
     * Set the fields Order for Data.
     * @return object Feedback
     */
    private function getOrderForQuery($q, $params = array()){

        if (array_key_exists('by_order', $params) && is_array($params['by_order'])) {
            
            $mode='';
            // Set Default Order OF Mode(ASC/DESC)
            if (array_key_exists('mode', $params['by_order']) && $params['by_order']['mode']!='') {
                $mode=$params['by_order']['mode'];
            }
            
            // Set Order By Fields & their Mode(ASC/DESC)
            if (array_key_exists('fields', $params['by_order'])) {

                // Set Order By Id
                if ($params['by_order']['fields']=='id') {
                    $q->orderBy('fd.id '.$mode);
                
                }
                
            }
        }else{
            $q->orderBy('fd.name');
        }

        // Return Object After Set Orders & Mode(ASC/DESC)
        return $q;
    }
}

?>
<?php

namespace VM\MakerBundle\Repository;

use VM\GeneralBundle\Model\RepositoryModel;
use Doctrine\ORM\EntityRepository;

class AdvertRepository extends EntityRepository {
    
    ################### Optimize Lib Use Of Generic Function ################################

    /*****
    * Base function to get all the datas for getting Advert
    * @param array $params
    * @return Doctrine_Query
    */

    public function getElements($params){

        $q = $this->createQueryBuilder('ad');

        $m = new RepositoryModel($q, $params);
        
        $q = $m->getQ();

        $this->getFieldsForQuery($q, $params);

        $this->getByParamsForQuery($q, $params);

        return $m->finalize();
    }
    
    /**
    * Get the fields action for the Query Select.
    * @return object Advert
    */
    private function getFieldsForQuery($q, $params){

        // Get Length of Level
        $model = New RepositoryModel($q);
        $length = $model->getLength();
        
        $q->leftJoin('ad.Maker','en');
               
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
     * @return object Advert
     */
    private function getByParamsForQuery($q, $params = array()){
        
        // Filter By Id
        if (array_key_exists('by_id', $params)) {
            $q->where($q->expr()->eq('ad.id',$params['by_id']));
        }
        // Filter By Slug
        if (array_key_exists('by_slug', $params)) {            
            $q->andWhere($q->expr()->eq('ad.slug',"'".$params['by_slug']."'"));
        }
        if(array_key_exists('adv_id_in', $params)){
            $q->andWhere($q->expr()->in('ad.id',$params['adv_id_in']));
        }
        // Filter By Maker id
        if (array_key_exists('by_maker_id', $params)) {            
            $q->andWhere($q->expr()->eq('en.id',$params['by_maker_id']));
        }


        // Return Object After Set Filters
        return $q;
    }
    
}

?>
<?php

namespace SO\MovieBundle\Repository;
use SO\GeneralBundle\Model\RepositoryModel;
use SO\StandardBundle\Repository\StandardRepository;
use Doctrine\ORM\EntityRepository;

class LinkRepository extends StandardRepository {

    public function getElements($params){

        $q = $this->createQueryBuilder('l');

        $this->getFieldsForQuery($q, $params);

        $this->getByParamsForQuery($q, $params);

        return $this->getActionsForQuery($q, $params);
    }
    ################### Optimize Lib Use Of Generic Function ################################
    /**
     * Get the fields action for the Query Select.
     * @return object AssociationTable
     */
    private function getFieldsForQuery($q, $params) {
        // Get Level Of Length
        $length = 2;
        // Get Tiny Data
        if ($length >= 1) {
            // Get Small Data 
            if ($length >= 2) {
                $q = $q->leftJoin('l.Movie','m');
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
     * @return object CategorieTable
     */
    private function getByParamsForQuery($q, $params = array()) {
         // Get By Id
        if (array_key_exists('by_mixture', $params)) {            
            $q->where($q->expr()->eq('l.mixture', $params['by_mixture']));
        }
        if (array_key_exists('by_purevid', $params)) {            
            $q->where($q->expr()->eq('l.purevid', $params['by_purevid']));
        }
         if (array_key_exists('by_movie_code', $params)) {            
            $q->where($q->expr()->eq('m.code', $params['by_movie_code']));
        }
        
/*
        // Get By Id
        if (array_key_exists('by_id', $params)) {            
            $q->where($q->expr()->eq('m.id', $params['by_id']));
        }
        // Get By id In
        if (array_key_exists('by_id_in', $params)) {            
            $q->andWhere($q->expr()->in('m.id', $params['by_id_in']));
        }
        
         // Get By Categorie Id 
        if (array_key_exists('by_category', $params) && $params['by_category']!='') {
            $q->andWhere($q->expr()->eq('m.LinkCategory', $params['by_category']));
        }
        
         // Get By Template
        if (array_key_exists('by_template', $params) && $params['by_template']!='') {
            $q->andWhere($q->expr()->eq('m.template', "'".$params['by_template']."'"));
        }
        
        // Get By Name of Title (approximate)
        if (array_key_exists('by_keyword', $params)) {
            $q->andWhere($q->expr()->like('m.name', "'".$params['by_keyword']."%'"));
        }
  */     
        // Return Object After Set Filters
        return $q;

    }
}
?>
<?php

namespace SO\MovieBundle\Repository;
use SO\GeneralBundle\Model\RepositoryModel;
use Doctrine\ORM\EntityRepository;

class MovieRepository extends EntityRepository {

    public function getElements($params){

        $q = $this->createQueryBuilder('ec');

        $m = new RepositoryModel($q, $params);
        //$m->changeBase(array('name' => 'en.name'));
        $q = $m->getQ();

        $this->getFieldsForQuery($q, $params);

        $this->getByParamsForQuery($q, $params);

        return $m->finalize();
    }
    ################### Optimize Lib Use Of Generic Function ################################

    /*     * ***
     * Base function to get all the datas for getting an Movies
     * @param array $params
     * @return Doctrine_Query
     */

    public function getMovies($params = array()) {
        $params['alias']= 'm';
        // Create Instance of StdDiplomaNature Model
        $q = $this->createQueryBuilder($params['alias']);

        // Get All Fields & Extra Fields
        $this->getFieldsForQuery($q, $params);

        // Get All Data By Filters
        $this->getByParamsForQuery($q, $params);

       // Set Limits Of Data
        $this->getLimitForQuery($q, $params);

        // Fetch Data By Actions Param
        return $this->getActionsForQuery($q, $params);
    }

    /**
     * Get the fields action for the Query Select.
     * @return object AssociationTable
     */
    private function getFieldsForQuery($q, $params) {
        // Get Level Of Length

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
     * @return object CategorieTable
     */
    private function getByParamsForQuery($q, $params = array()) {
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
            $q->andWhere($q->expr()->eq('m.MovieCategory', $params['by_category']));
        }
        
         // Get By Template
        if (array_key_exists('by_template', $params) && $params['by_template']!='') {
            $q->andWhere($q->expr()->eq('m.template', "'".$params['by_template']."'"));
        }
        
        // Get By Name of Title (approximate)
        if (array_key_exists('by_keyword', $params)) {
            $q->andWhere($q->expr()->like('m.name', "'".$params['by_keyword']."%'"));
        }
       
        // Return Object After Set Filters
        return $q;
*/
    }
}
?>
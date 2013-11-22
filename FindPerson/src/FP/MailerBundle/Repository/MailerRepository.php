<?php

namespace FP\MailerBundle\Repository;
use FP\GeneralBundle\Model\RepositoryModel;
use Doctrine\ORM\EntityRepository;

class MailerRepository extends EntityRepository {

    public function getElements($params){

        $q = $this->createQueryBuilder('m');

        $m = new RepositoryModel($q, $params);
        
        $q = $m->getQ();

        $this->getFieldsForQuery($q, $params);

        $this->getByParamsForQuery($q, $params);

        return $m->finalize();
    }
    ################### Optimize Lib Use Of Generic Function ################################

    /*     * ***
     * Base function to get all the datas for getting an Mailers
     * @param array $params
     * @return Doctrine_Query
     */

    public function getMailers($params = array()) {
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

        $q->leftJoin('m.MailerCategory', 'mc');
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
            if(is_array($params['by_category'])){
                if(array_key_exists('id', $params['by_category']) && $params['by_category']['id']) {
                    $q->where($q->expr()->eq('mc.id',$params['by_category']['id']));
                }elseif(array_key_exists('name', $params['by_category']) && $params['by_category']['name']){
                    $q->where($q->expr()->like('mc.name',"'".$params['by_category']['name']."%'"));
                }
            }else{
                $q->andWhere($q->expr()->eq('m.MailerCategory', $params['by_category']));
            }
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
/**/
    }

   
}
?>
<?php

namespace VM\MakerBundle\Repository;

use VM\GeneralBundle\Model\RepositoryModel;
use Doctrine\ORM\EntityRepository;

class Makerepository extends EntityRepository {
    
    ################### Optimize Lib Use Of Generic Function ################################

    /*****
    * Base function to get all the datas for getting Maker
    * @param array $params
    * @return Doctrine_Query
    */

    public function getElements($params){

        $q = $this->createQueryBuilder('en');

        $m = new RepositoryModel($q, $params);
        //$m->changeBase(array('name' => 'en.name'));
        $q = $m->getQ();

        $this->getFieldsForQuery($q, $params);

        $this->getByParamsForQuery($q, $params);

        return $m->finalize();
    }
    
    /**
    * Get the fields action for the Query Select.
    * @return object Maker
    */
    private function getFieldsForQuery($q, $params){

        // Get Length of Level
        $model = New RepositoryModel($q);
        $length = $model->getLength();
        
        $q->leftJoin('en.MakerAdministrator','ea');
        $q->leftJoin('en.MakerNote', 'n');
               
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
     * @return object Maker
     */
    private function getByParamsForQuery($q, $params = array()){
        
        // Filter By Id
        if (array_key_exists('by_id', $params)) {
            $q->where($q->expr()->eq('en.id',$params['by_id']));
        }
        // Filter By Slug
        if (array_key_exists('by_slug', $params)) {            
            $q->andWhere($q->expr()->eq('en.slug',"'".$params['by_slug']."'"));
        }
        // Filter By Id In
        if(array_key_exists('ent_id_in', $params)){
            $q->andWhere($q->expr()->in('en.id',$params['ent_id_in']));
        }
        // Filter By Commercial
        if (array_key_exists('by_commercial_status', $params) && $params['by_commercial_status'] != '') {
            $q->andWhere($q->expr()->like('en.status','\'%"commercial";s:'.strlen($params['by_commercial_status']).':"'.$params['by_commercial_status'].'"%\''));
        }
        // Filter By Special Staus
        if (array_key_exists('by_special_status', $params) && $params['by_special_status'] != '') {
            $q->andWhere($q->expr()->like('en.status','\'%"special";s:'.strlen($params['by_special_status']).':"'.$params['by_special_status'].'"%\''));
        }
        // Filter By Collaborator
        if (array_key_exists('by_with_collaborator', $params) && $params['by_with_collaborator'] != '') {
           
            $q->leftJoin('ea.User','eauC');
            // With MakerAdministrator As (Collaborator)
            if ($params['by_with_collaborator'] == 'true') {
                $q->andWhere($q->expr()->like('ea.roles','\'%"ROLE_COLLAB"%\''));            
            // Without MakerAdministrator As (Collaborator)
            } else if($params['by_with_collaborator'] == 'false') {
                $q->andWhere($q->expr()->orX($q->expr()->isNull('ea.roles'),$q->expr()->like('ea.roles','\'%"ROLE_SALER"%\'')));
            }
        }
        // Filter By Gestionnaire
        if (array_key_exists('by_gestionnaire', $params) && $params['by_gestionnaire'] != '') {
            // Relation with MakerAdministrator & User
            $q->leftJoin('ea.User','eauG');
            // Without MakerAdministrator User
            if ($params['by_gestionnaire'] == 0) {
                $q->andWhere($q->expr()->isNull('eauG.id'));            
            // With MakerAdministrator User as SALER   
            } else {
                $q->andWhere($q->expr()->eq('eauG.id',$params['by_gestionnaire']));
            }
        }
        
        
        if (array_key_exists('by_note_active', $params) && $params['by_note_active'] != '') {
            $q->andWhere($q->expr()->orX($q->expr()->isNull('n.is_close'),$q->expr()->eq('n.is_close',0)))
                    ->andWhere($q->expr()->orX($q->expr()->isNull('n.is_main'),$q->expr()->eq('n.is_main',0)));

            if ($params['by_note_active'] == -10)
                $q->andWhere($q->expr()->gte('DATE_DIFF(CURRENT_DATE(),n.date_recall)',10));
            else if ($params['by_note_active'] == -7)
                $q->andWhere($q->expr()->between('DATE_DIFF(CURRENT_DATE(),n.date_recall)',7,9));
            else if ($params['by_note_active'] == -3)
                $q->andWhere($q->expr()->between('DATE_DIFF(CURRENT_DATE(),n.date_recall)',3,6));
            else if (in_array($params['by_note_active'], range(-2, 2)))
                $q->andWhere($q->expr()->eq('DATE_DIFF(CURRENT_DATE(),n.date_recall)', ($params['by_note_active'] < 0 ? -($params['by_note_active']) : -$params['by_note_active'])));
            else if ($params['by_note_active'] == 3)
                $q->andWhere($q->expr()->between('DATE_DIFF(CURRENT_DATE(),n.date_recall)',-3,-6));
            else if ($params['by_note_active'] == 7)
                $q->andWhere($q->expr()->between('DATE_DIFF(CURRENT_DATE(),n.date_recall)',-7,-9));
            else if (intval($params['by_note_active']) >= 10)
                $q->andWhere($q->expr()->lte('DATE_DIFF(CURRENT_DATE(),n.date_recall)',-10));
        }


        // Return Object After Set Filters
        return $q;
    }
}

?>
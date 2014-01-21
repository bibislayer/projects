<?php

namespace VM\MakerBundle\Repository;

use VM\GeneralBundle\Model\RepositoryModel;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query as Q;
use VM\MakerBundle\Entity\MakerNote;

class MakerNoteRepository extends EntityRepository {
    
    public function getNoteAgee($params=array()){
        
        $q = $this->createQueryBuilder('n');
                $q->select('SUM((CASE WHEN(DATE_DIFF(CURRENT_DATE(),n.date_recall)>=10) THEN 1 ELSE 0 END)) AS nbjour_after_ten_days,
                SUM((CASE WHEN(DATE_DIFF(CURRENT_DATE(),n.date_recall) BETWEEN 7 AND 9) THEN 1 ELSE 0 END)) AS nbjour_after_seven_days,
                SUM((CASE WHEN(DATE_DIFF(CURRENT_DATE(),n.date_recall) BETWEEN 3 AND 6) THEN 1 ELSE 0 END)) AS nbjour_after_three_days,
                SUM((CASE WHEN(DATE_DIFF(CURRENT_DATE(),n.date_recall)=2) THEN 1 ELSE 0 END)) AS nbjour_day_after_tomorrow,
                SUM((CASE WHEN(DATE_DIFF(CURRENT_DATE(),n.date_recall)=1) THEN 1 ELSE 0 END)) AS nbjour_tomorrow,
                SUM((CASE WHEN(DATE_DIFF(CURRENT_DATE(),n.date_recall)=0) THEN 1 ELSE 0 END)) AS nbjour_today,
                SUM((CASE WHEN(DATE_DIFF(CURRENT_DATE(),n.date_recall)=-1) THEN 1 ELSE 0 END)) AS nbjour_yesterday,
                SUM((CASE WHEN(DATE_DIFF(CURRENT_DATE(),n.date_recall)=-2) THEN 1 ELSE 0 END)) AS nbjour_before_two_days,
                SUM((CASE WHEN(DATE_DIFF(CURRENT_DATE(),n.date_recall) BETWEEN -3 AND -6) THEN 1 ELSE 0 END)) AS nbjour_before_three_days,
                SUM((CASE WHEN(DATE_DIFF(CURRENT_DATE(),n.date_recall) BETWEEN -7 AND -9) THEN 1 ELSE 0 END)) AS nbjour_before_seven_days,
                SUM((CASE WHEN(DATE_DIFF(CURRENT_DATE(),n.date_recall)<=-10) THEN 1 ELSE 0 END)) AS nbjour_before_ten_days')
                ->where($q->expr()->andX($q->expr()->orX($q->expr()->isNull('n.is_close'),$q->expr()->eq('n.is_close', 0)),$q->expr()->orX($q->expr()->isNull('n.is_main'),$q->expr()->eq('n.is_main', 0))));
        
                $m = new RepositoryModel($q, $params);
                
        return $m->finalize();
    }


    ################### Optimize Lib Use Of Generic Function ################################

    /*****
    * Base function to get all the datas for getting MakerNote
    * @param array $params
    * @return Doctrine_Query
    */
    public function getElements($params=array()){
        
        $q = $this->createQueryBuilder('n');

        $m = new RepositoryModel($q, $params);
        
        //$q = $m->getQ();

        $this->getFieldsForQuery($q, $params);

        $this->getByParamsForQuery($q, $params);
        
        return $m->finalize();
    }
    
    /**
    * Get the fields action for the Query Select.
    * @return object MakerNote
    */
    private function getFieldsForQuery($q, $params){

        $model = New RepositoryModel($q);
        $length = $model->getLength();
        
        $q->leftJoin('n.Maker','en');
        $q->leftJoin('n.User','u');
               
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
     * @return object MakerNote
     */
    private function getByParamsForQuery($q, $params = array()){
        
        // Filter By Id
        if (array_key_exists('by_id', $params)) {
            $q->where($q->expr()->eq('n.id',$params['by_id']));
        }
        // Filter By Maker Id
        if (array_key_exists('by_maker_id', $params)) {
            $q->andWhere($q->expr()->eq('en.id',$params['by_maker_id']));
        }
        // Filter By is main
        if (array_key_exists('by_is_main', $params)) {
            if($params['by_is_main'])
                $q->andWhere($q->expr()->eq('n.is_main',$params['by_is_main']));
            else
                $q->andWhere($q->expr()->orX($q->expr()->isNull('n.is_main'),$q->expr()->eq('n.is_main',0)));
        }
        // Filter By User Id
        if (array_key_exists('by_user_id', $params)) {
            $q->andWhere($q->expr()->eq('u.id',$params['by_user_id']));
        }

        // Return Object After Set Filters
        return $q;
    }
}

?>
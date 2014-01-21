<?php

namespace VM\QuestionnaireBundle\Repository;
use VM\GeneralBundle\Model\RepositoryModel;
use Doctrine\ORM\EntityRepository;

class QuestionnaireRepository extends EntityRepository {
    
     ################### Optimize Lib Use Of Generic Function ################################
      public function getElements($params = array()){

        $q = $this->createQueryBuilder('qu');

        $m = new RepositoryModel($q, $params);
        //$m->changeBase(array('name' => 'en.name'));
        $q = $m->getQ();

        $this->getFieldsForQuery($q, $params);

        $this->getByParamsForQuery($q, $params);

        return $m->finalize();
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
     * @return object QuestionnaireTable
     */
    private function getByParamsForQuery($q, $params = array()) {
        
        // Get By Autocomplete Enterprise Id
        if (array_key_exists('by_enterprise', $params) && array_key_exists('id', $params['by_enterprise']) && $params['by_enterprise']['id']!='') {    
            $q->andWhere($q->expr()->eq('qu.Enterprise', $params['by_enterprise']['id']));
        }
        // Get By Enterprise Id
        if (array_key_exists('by_ent_id', $params)) {            
            $q->andWhere($q->expr()->eq('qu.Enterprise', $params['by_ent_id']));
        }
        if (array_key_exists('by_slug', $params)) {            
            $q->andWhere($q->expr()->eq('qu.slug', "'".$params['by_slug']."'"));
        }
        // Get By Questionnaire Type Id
        if (array_key_exists('by_type', $params)) {            
            $q->andWhere($q->expr()->eq('qu.StdQuestionnaireType', $params['by_type']));
        }
        // Get By Has Respondant
        if (array_key_exists('by_has_respondant', $params)) {
            $q->leftJoin('qu.QuestionnaireUser','resp');
            
            if($params['by_has_respondant']=='true'){
                $q->andWhere($q->expr()->isNotNull('resp.id'));
            }else if($params['by_has_respondant']=='false'){
                $q->andWhere($q->expr()->isNull('resp.id'));
            }            
        }
        // Get By Status
        if (array_key_exists('by_status', $params)) {
            if($params['by_status'] == 'published'){
                $q->andWhere('(qu.is_end_close = 0 OR qu.is_end_close IS NULL) AND qu.published = 1 AND qu.approbation = 1');
            }elseif($params['by_status'] == 'closed'){
                $q->andWhere('qu.is_end_close = 1');
            }elseif($params['by_status'] == 'creation'){
                $q->andWhere('(qu.published = 0 OR qu.published IS NULL) AND (qu.approbation = 0 OR qu.approbation IS NULL)');
            }elseif($params['by_status'] == 'need_validate'){
                $q->andWhere('qu.published = 1 AND (qu.approbation = 0 OR qu.approbation IS NULL)');
            }elseif($params['by_status'] == 'refused'){
                $q->andWhere('qu.approbation = 2');
            }
        }
        
        return $q;

    }
    
    
     //used for changing status of questionnaire like published, depublished etc
    public function changeStatus($params){
        
        $em = $this->getEntityManager();
         //getting object of questionnaire  
       
        $questionnaire = $this->getElements(array('by_slug' => $params['slug'], 'action' => 'one'));
        
        //if questionnaire exists then change status
        if ($questionnaire) {
            //setting status
            if($params['by_type'] == 'published'){
                 $questionnaire->setPublished(1); 
                
            }else if($params['by_type'] == 'unpublished'){
                 $questionnaire->setPublished(0);                  
            }
            
            $questionnaire->setPublishedAt(new \DateTime(date("Y-m-d h:i:s")));
           
            $em->persist($questionnaire);
            $em->flush();
        }        
    }
}

?>
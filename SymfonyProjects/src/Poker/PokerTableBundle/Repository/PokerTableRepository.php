<?php

namespace Poker\PokerTableBundle\Repository;
use Poker\GeneralBundle\Model\RepositoryModel;
use Doctrine\ORM\EntityRepository;

class PokerTableRepository extends EntityRepository {
    
     ################### Optimize Lib Use Of Generic Function ################################
      public function getElements($params = array()){

        $q = $this->createQueryBuilder('pt');

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
        $q->leftJoin('pt.PokerUser', 'pu');
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
     * @return object RecordingSessionTable
     */
    private function getByParamsForQuery($q, $params = array()) {
        
        // Get By Id
        if (array_key_exists('by_id', $params) && $params['by_id']!='') {    
            $q->andWhere($q->expr()->eq('pt.id', $params['by_id']));
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

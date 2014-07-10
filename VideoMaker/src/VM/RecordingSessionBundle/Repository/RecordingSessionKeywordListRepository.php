<?php

namespace VM\RecordingSessionBundle\Repository;

use VM\GeneralBundle\Model\RepositoryModel;
use Doctrine\ORM\EntityRepository;

class RecordingSessionKeywordListRepository extends EntityRepository {
    ################### Optimize Lib Use Of Generic Function ################################

    public function getElements($params = array()) {

        $q = $this->createQueryBuilder('rsl');

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
        $q->leftJoin('rsl.RecordingSession', 'rs');
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

        if (array_key_exists('by_recording_session', $params)) {
            $q->andWhere($q->expr()->eq('rs.slug', "'" . $params['by_recording_session'] . "'"));
        }
        if (array_key_exists('by_name', $params)) {
            $q->andWhere($q->expr()->eq('rsl.name', "'" . $params['by_name'] . "'"));
        }

        return $q;
    }

    //used for changing status of questionnaire like published, depublished etc
    public function changeStatus($params) {

        $em = $this->getEntityManager();
        //getting object of questionnaire  

        $questionnaire = $this->getElements(array('by_slug' => $params['slug'], 'action' => 'one'));

        //if questionnaire exists then change status
        if ($questionnaire) {
            //setting status
            if ($params['by_type'] == 'published') {
                $questionnaire->setPublished(1);
            } else if ($params['by_type'] == 'unpublished') {
                $questionnaire->setPublished(0);
            }

            $questionnaire->setPublishedAt(new \DateTime(date("Y-m-d h:i:s")));

            $em->persist($questionnaire);
            $em->flush();
        }
    }

}

?>

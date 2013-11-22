<?php

namespace FP\StandardBundle\Repository;

use FP\GeneralBundle\Model\RepositoryModel;
use Doctrine\ORM\EntityRepository;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;

/**
 * StdCategoryRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class StdCategoryRepository extends NestedTreeRepository
{
    ################### Optimize Lib Use Of Generic Function ################################

    /*     * ***
     * Base function to get all the datas for getting an Categorie
     * @param array $params
     * @return Doctrine_Query
     */

    public function getCategory($params = array()) {
        
        $q = $this->createQueryBuilder('c');
        
        $m = new RepositoryModel($q, $params);
        
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
        
        $model = New RepositoryModel($q);
        $length = $model->getLength();

        // Get Tiny Data
        if ($length >= 1) {

            // Get Small Data 
            if ($length >= 2) {
                    $q->leftJoin('c.parent', 'parent');
                // Get Extend Data
                if ($length >= 3) {

                    // Get Complete Data
                    if ($length >= 4) {
                        
                    }
                }
            }
        }

        // Get Extra Select
        if (array_key_exists('extra_select', $params) && is_array($params['extra_select'])) {

            // Get Count Categories By Params
            if (array_key_exists('count', $params['extra_select']) && is_array($params['extra_select']['count'])) {
                
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

        // Get By Categorie Id
        if (array_key_exists('by_id', $params)) {
            $q->where($q->expr()->eq('c.id', $params['by_id']));
        }

        // Get By Categorie Slug
        if (array_key_exists('by_slug', $params)) {
            $q->andWhere($q->expr()->eq('c.slug', "'".$params['by_slug']."'"));
        }

        // Get By Parent Id
        if (array_key_exists('by_parent_id', $params)) {
            $q->andWhere('c.parent_id = ?', $params['by_parent_id']);
        }

        // Get By Root Id
        if (array_key_exists('by_root_id', $params)) {
            $q->andWhere('c.root_id = ?', $params['by_root_id']);
        }

        // Get By Level Of Categorie
        if (array_key_exists('by_level', $params)) {
            $q->andWhere('c.level = ?', $params['by_level']);
        }
        // Get By Status Of Categorie
        if (array_key_exists('by_status', $params)) {
            // Check status with option `published`
            if ($params['by_status'] == 'published') {
                $approbation = 1;
                $published = 1;

                // Check staus with option `need_validate`
            } else if ($params['by_status'] == 'need_validate') {
                $approbation = 1;
                $published = 1;
                // Check staus with option `redac`
            } else if ($params['by_status'] == 'redac') {
                $approbation = 1;
                $published = 1;
                // Check staus with option `resp_redac`
            } else if ($params['by_status'] == 'resp_redac') {
                $approbation = 1;
                $published = 1;
                // Check staus with option `unpublished`
            } else if ($params['by_status'] == 'unpublished') {
                $approbation = 0;
                $published = 0;
            }

            $q->andWhere('c.approbation = :approbation')->setParameter('approbation', $approbation);
            $q->andWhere('c.published = :published')->setParameter('published', $published);
        }



        // Return Object After Set Filters
        return $q;
    }
}

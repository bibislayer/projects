<?php

namespace FP\MailerBundle\Repository;
use FP\GeneralBundle\Model\RepositoryModel;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
use Doctrine\ORM\EntityRepository;

class MailerCategoryRepository extends EntityRepository{

    public function getElements($params){

        $q = $this->createQueryBuilder('ec');

        $m = new RepositoryModel($q, $params);
        //$m->changeBase(array('name' => 'en.name'));
        $q = $m->getQ();

        //$this->getFieldsForQuery($q, $params);

        //$this->getByParamsForQuery($q, $params);

        return $m->finalize();
    }
    ################### Optimize Lib Use Of Generic Function ################################

    /*     * ***
     * Base function to get all the datas for getting an Categorie
     * @param array $params
     * @return Doctrine_Query
     */

    public function getMailerCategory($params = array()) {
        $params['alias']= 'c';
        // Create Instance of StdDiplomaNature Model
        $q = $this->createQueryBuilder($params['alias']);

        // Get All Fields & Extra Fields
        $this->getFieldsForQuery($q, $params);

        // Get All Data By Filters
        $this->getByParamsForQuery($q, $params);

        // Set Order By Field & Mode(ASC/DESC)
        $this->getOrderForQuery($q, $params);

        $model = New ModelRepository();
        // Set Limits Of Data
        $model->getLimitForQuery($q, $params);

        // Fetch Data By Actions Param
        return $model->getActionsForQuery($q, $params);
    }

    /**
     * Get the fields action for the Query Select.
     * @return object AssociationTable
     */
    private function getFieldsForQuery($q, $params) {
        // Get Level Of Length

        $model = New RepositoryModel();
        $length = $model->getLengthForQuery($q, $params);

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
        // Get By Categorie Id In
        if (array_key_exists('by_id_in', $params)) {            
            $q->andWhere($q->expr()->in('c.id', $params['by_id_in']));
        }
        // Get By Name of Title (approximate)
        if (array_key_exists('by_keyword', $params)) {
            $q->andWhere($q->expr()->like('c.name', "'".$params['by_keyword']."%'"));
        }
     
        // Get By Parent Id
        if (array_key_exists('by_parent_id', $params)) {
            $q->andWhere($q->expr()->eq('c.parent_id', $params['by_parent_id']));
        }

        // Get By Root Id
        if (array_key_exists('by_root_id', $params)) {
            $q->andWhere($q->expr()->eq('c.root_id', $params['by_root_id']));
        }
        

        // Get By Level Of Categorie
        if (array_key_exists('by_level', $params)) {
            $q->andWhere($q->expr()->eq('c.level', $params['by_level']));
        }


        // Return Object After Set Filters
        return $q;
    }

    /**
     * Set the fields Order for Data.
     * @return object CategorieTable
     */
    private function getOrderForQuery($q, $params = array()) {

        if (array_key_exists('by_order', $params) && is_array($params['by_order'])) {

            $mode = '';
            // Set Default Order OF Mode(ASC/DESC)
            if (array_key_exists('mode', $params['by_order']) && $params['by_order']['mode'] != '') {
                $mode = $params['by_order']['mode'];
            }

            if (array_key_exists('fields', $params['by_order'])) {

                // Set Order By Id
                if ($params['by_order']['fields'] == 'id') {
                    $q->orderBy('c.id ' . $mode);

                    // Set Order By Name     
                } else if ($params['by_order']['fields'] == 'name') {
                    $q->orderBy('c.name ' . $mode);

                    // Set Order By Parent Id   
                } else if ($params['by_order']['fields'] == 'parent_id') {
                    $q->orderBy('c.parent_id ' . $mode);
                }
            }
        } else {
            $q->orderBy('c.name');
        }

        // Return Object After Set Orders & Mode(ASC/DESC)
        return $q;
    }

    
    
     //returns the subcategory listing by one level below
    public function getMailerCategoryChildByOneLevel($lft, $rgt, $root, $level) {
        $level = $level + 1;

        return $this->createQueryBuilder('c')
                        ->where('c.lft > ' . $lft)
                        ->andWhere('c.rgt < ' . $rgt)
                        ->andWhere('c.root_id = ' . $root)
                        ->andwhere('c.level = ' . $level)
                        ->orderBy('c.level')
                        ->getQuery()
                        ->getResult();
    }

    //returns parent name of subcategory
    public function getMailerCategoryParentNameTree($root, $lft, $rgt) {
        
        $record = $this->createQueryBuilder('c')
                ->where('c.lft < :lft')
                ->andWhere('c.rgt > :rgt')
                ->andWhere('c.root_id = :root')
                ->setParameters(array('root' => $root, 'lft' => $lft, 'rgt' => $rgt))
                ->orderBy('c.level')
                ->getQuery()
                ->getResult();

        $parent_name = '';
        $count = 0;

        //making parentlist using >> symbol
        foreach ($record as $rec) {    
          
            if ($count == 0)
                $parent_name .= $rec->getName();
            else
                $parent_name .= ' <b> >> </b> ' . $rec->getName();
            $count++;
        }

        return $parent_name;
    }
}
<?php

namespace VM\StandardBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

class StandardRepository extends EntityRepository {
    /*     * ****
     * This function set the limit for the query
     * @param $q
     * @param $params
     * @return Doctrine_Query
     */

    function getLimitForQuery($q, $params = array()) {
        
        if (array_key_exists('limit', $params)) {
            return $q->setMaxResults($params['limit']);
        }
    }

    /*     * ******
     * This function allows to return an int for determinate the length of the select
     * @param array $params
     * @return int
     */

    function getLengthForQuery($params = array()) {
        
        $length = 2;
        if (array_key_exists('length', $params)) {
            if ($params['length'] == 'tiny') {
                $length = 0;
            } elseif ($params['length'] == 'small') {
                $length = 1;
            } elseif ($params['length'] == 'extend') {
                $length = 3;
            } elseif ($params['length'] == 'complete') {
                $length = 4;
            } elseif ($params['length'] == 'all') {
                $length = 5;
            }
        }
        return $length;
    }

    /*     * ****
     * This function return the action to do for the query
     * @param $q
     * @param $params
     * @return Doctrine_Query
     */

    function getActionsForQuery($q, $params = array()) {
       
        if (array_key_exists('action', $params)) {
            $params['actions'] = $params['action'];
        }

        if (array_key_exists('actions', $params) && $params['actions'] == 'paginate') {
            $q->setFirstResult(($params['page'] - 1) * $params['max_items_on_listepage']);
            $q->setMaxResults($params['max_items_on_listepage']);
            return new Paginator($q);
        } elseif (array_key_exists('actions', $params) && $params['actions'] == 'count') {
            $q->select('count('.$params['alias'].'.id)');
            return $q->getQuery()->getSingleScalarResult();
        } elseif (array_key_exists('actions', $params) && $params['actions'] == 'one') {
            return $q->getQuery()->getOneOrNullResult();
        } elseif (array_key_exists('actions', $params) && $params['actions'] == 'array') {
            return $q->fetchArray();
        } elseif (array_key_exists('actions', $params) && $params['actions'] == 'execute') {
            return $q->getQuery()->getResult();
        } else {
            return $q->getQuery()->getResult();
        }
    }

    /**
     * FUNCTION PART
     *
     * @return object ArticleTable
     */
    function getOrder($q, $params = array()) {

        // Set Order
        if (array_key_exists('order', $params)) {
            if (array_key_exists('random', $params['order']) && $params['order']['random'] == true) {
                $q->orderBy('RAND()');
            } else {
                $order_field = 'a.name';
                if (array_key_exists('field', $params['order'])) {
                    if ($params['order']['field'] == 'name') {
                        $order_field = 'a.name';
                    }
                    if ($params['order']['field'] == 'published') {
                        $order_field = 'a.published_at';
                    }
                }
                if (array_key_exists('option', $params['order'])) {
                    $order_option = $params['order']['option'];
                } else {
                    $order_option = 'ASC';
                }

                $q->orderBy($order_field . ' ' . $order_option);
            }
        }
        return $q;
    }

}

?>
<?php

namespace SO\GeneralBundle\Model;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\DependencyInjection\ContainerInterface;

class RepositoryModel extends EntityRepository
{
    private $params = array();
    private $action;
    private $order = array('field' => 'code', 'sort' => 'ASC');
    private $length = 2;

    function __construct($q, $params = array()) {

        $this->params = $params;
        $this->aModel = $q->getRootAliases();
        $this->aModel = $this->aModel[0];
        $this->q = $q;
        $this->setLength();
        $this->getAction();

    }
    private function getByBase() {
        $q = $this->q;
        $this->name = (isset($this->name))?$this->name:$this->aModel.'.name';

        if (array_key_exists('by_keyword', $this->params) && $this->params['by_keyword']){
            //$q->andWhere($this->aModel.'.name LIKE \''.$this->params['by_keyword'].'%\'');
            $q->andWhere($q->expr()->like($this->name, "'".$this->params['by_keyword']."%'"));
        }
        if (array_key_exists('by_approximate_keyword', $this->params) && $this->params['by_approximate_keyword']){
            $q->andWhere($q->expr()->like($this->name, "'%".$this->params['by_approximate_keyword']."%'"));
        }
        if (array_key_exists('by_name', $this->params) && $this->params['by_name']){
            $q->andWhere($q->expr()->eq($this->name, "'".$this->params['by_name']."'"));
        }
        if (array_key_exists('by_id', $this->params) && $this->params['by_id']){
            $q->andWhere($q->expr()->eq($this->aModel.'.id', "'".$this->params['by_id']."'"));
        }
        if (array_key_exists('by_not_id', $this->params) && $this->params['by_not_id']){
            $q->andWhere($this->aModel.'.id != '.$this->params['by_not_id'].'%');
        }
        if (array_key_exists('by_id_in', $this->params) && $this->params['by_id_in']){
            $q->andWhere($q->expr()->in($this->aModel.'.id', "'".$this->params['by_id_in']."'"));
        }
        if (array_key_exists('by_not_id_in', $this->params) && $this->params['by_not_id_in']){
            $q->andWhere($q->expr()->notIn($this->aModel.'.id', "'".$this->params['by_not_id_in']."'"));
        }
        if (array_key_exists('by_slug', $this->params) && $this->params['by_slug']){
            $q->andWhere($q->expr()->eq($this->aModel.'.slug', "'".$this->params['by_slug']."'"));
        }
        $this->q = $q;
    }

    public function changeBase($param) {
        if (array_key_exists('name', $param)){
            $this->order['field'] = $param['name'];
            $this->name = $param['name'];
        }
    }
    private function getAction() {
        if (array_key_exists('action', $this->params) && $this->params['action']){
            $this->action = $this->params['action'];
        }else{
            $this->action = 'execute';
        }
    }
    private function setLength() {
        if(array_key_exists('length', $this->params) && $this->params['length']){
            switch ($this->params['length']):
                case 'tiny':
                    $this->length = 0;
                    break;
                case 'small':
                    $this->length = 1;
                    break;
                case 'normal':
                    $this->length = 2;
                    break;
                case 'extend':
                    $this->length = 3;
                    break;
                case 'complete':
                    $this->length = 4;
                    break;
                case 'all':
                    $this->length = 5;
                    break;
            endswitch;
        }
    }
    public function getLength() {
        return $this->length;
    }
    public function getQ() {
        return $this->q;
    }
    public function setOrder() {
        if (strpos($this->order['field'],'.') !== false) {
            $this->q->orderBy($this->order['field'], $this->order['sort']);
        }else{
            $this->q->orderBy($this->aModel.'.'.$this->order['field'], $this->order['sort']);
        }
    }
    public function finalize() {

        $this->getByBase();
        $this->setOrder();

        if(array_key_exists('limit', $this->params) && $this->params){
            $this->q->setMaxResults($this->params['limit']);
        }
        $q = $this->q->getQuery();
        switch ($this->action):
            case 'count':
                $q = $q->getSingleScalarResult();
                break;
            case 'paginate':
                break;
            case 'one':
                $q = $q->getOneOrNullResult();
                break;
            case 'execute':
                $q = $q->getResult();
                break;
        endswitch;
        return $q;
    }

}

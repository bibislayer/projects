<?php

namespace VM\GeneralBundle\Form\FormListener;

use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;


class AutocompleteListener implements EventSubscriberInterface
{

    protected $url = array();
    protected $repository;
    protected $class;
    protected $options = array();
    protected $allowed_values;
    protected $om;

    public function __construct($om, $options)
    {
        $this->om = $om;
        $this->options = $options;
        if($options['data_class']){
            $this->class = $options['data_class'];
        }
        $this->allowed_values = $options['allowed_values'];
    }

    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::SUBMIT => array('onSubmit'),
        );
    }

    public function preSetData(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();
        $type = gettype($data);

        if($data){
            if($type == 'object'){
                $name = $data->getName();
                $id = $data->getId();
            }elseif($type == 'string'){
                $name = $data;
                $id = '';
            }
        }else{
            $name = '';
            $id = '';
        }

        if($this->class){
            $form->add('name', 'text', array('mapped' => true, 'data' => $name));
            $form->add('id', 'hidden', array('mapped' => false, 'data' => $id));
        }else{
            $form->add('name', 'text');
            $form->add('id', 'hidden');
        }


        return null;
    }

    public function onSubmit(FormEvent $event)
    {

        $form = $event->getForm();

        $id = $form->get('id')->getData();
        $name = $form->get('name')->getData();

        switch($this->allowed_values):
            case 'id':
                if($this->class){
                    $ms = $this->om->getRepository($this->class)->findOneBy(array('id' => $id));
                }else{
                    $ms = $id;
                }
            break;
            case 'name':
                $ms = $name;
            break;
            case 'both':
                if($id){
                    if($this->class){
                        $ms = $this->om->getRepository($this->class)->findOneBy(array('id' => $id));
                    }else{
                        $ms = $id;
                    }
                }else{
                    $ms = $name;
                }
            break;

        endswitch;

        $event->setData($ms);
    }

}
?>
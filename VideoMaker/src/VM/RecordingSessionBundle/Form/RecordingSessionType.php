<?php

namespace VM\RecordingSessionBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormError;
use Symfony\Bridge\Doctrine\RegistryInterface;
use VM\RecordingSessionBundle\Form\RecordingSessionKeywordListType;

class RecordingSessionType extends AbstractType {

    private $builder = '';
    private $doctrine;

    public function __construct(RegistryInterface $doctrine) {
        $this->doctrine = $doctrine;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {

        $builder->add('name', null, array('attr' => array('class' => 'form-control', 'placeholder' => 'Nom')));

        $builder->add('RecordingSessionKeywordList', 'collection', array(
            'type' => new RecordingSessionKeywordListType(),
            'allow_add' => true,
            'by_reference' => false,
            'allow_delete' => true,
        ));
        //If environment is backend
        if (array_key_exists('env', $options) && $options['env'] == 'bo') {
            $builder->add('category', 'nested_select', array(
                'label' => 'Nested Data',
                'mapped' => false,
                'empty_value' => 'Catégories',
                'label' => 'Catégories',
                'required' => false,
                'config' => array('list' => $this->getCategoryData(), 'type' => 'category', 'line' => '<option value="{{ element.id }}">{{ element.name }}</option>')
            ));
        }

        $builder->add('text_introduction', 'textarea', array('attr' => array('class' => 'form-control', 'rows' => '3')));
        $builder->add('text_presentation', 'textarea', array('attr' => array('class' => 'form-control', 'rows' => '3')));


        $builder->add('save', 'submit', array('label' => 'Enregistrer votre session', 'attr' => array('class' => 'btn btn-success')));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'VM\RecordingSessionBundle\Entity\RecordingSession',
            'cascade' => true,
            'formule' => 1,
            'no_enterprise' => 0,
            'env' => 'bo'
        ));
    }

    public function getName() {
        return 'recording_session';
    }

    //Get category list of level 0 and published 
    public function getCategoryData() {
        return $this->doctrine->getRepository('VMStandardBundle:StdCategory')->getCategory(array('by_level' => 0, 'by_order' => array('mode' => 'ASC', 'field' => 'name')));
    }

}

?>

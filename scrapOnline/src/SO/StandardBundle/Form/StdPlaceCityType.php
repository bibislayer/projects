<?php

namespace SO\StandardBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class StdPlaceCityType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        
        $builder->add('Department','entity',array(
            'class' => 'SOStandardBundle:StdPlaceDepartment',
            'property' => 'name',
            'empty_value' => 'Tous',
            'invalid_message'=>'Department can not be blank',
            'label' => 'Department',
            'attr' => array('class' => '_70'),
            'required'=>false
        ));
       $builder->add('name', 'text', array('label' => 'Nom', 'attr' => array('placeholder' => "Nom", 'class' => '_70')));
       
       $builder->add('zip_code', 'text', array('label' => 'Zip code','required'=>false, 'attr' => array('placeholder' => "Zip code", 'class' => '_70')));
       
       $builder->add('latitude', 'text', array('label' => 'Latitude','required'=>false, 'attr' => array('placeholder' => "Latitude", 'class' => '_70')));
       
       $builder->add('longitude', 'text', array('label' => 'Longitude','required'=>false, 'attr' => array('placeholder' => "Longitude", 'class' => '_70')));
       $builder->add('save', 'submit', array('label' => 'Enregistrer'));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'SO\StandardBundle\Entity\StdPlaceCity',
            'cascade_validation' => true,
        ));
    }

    public function getName() {
        return 'place_city';
    }
    

}
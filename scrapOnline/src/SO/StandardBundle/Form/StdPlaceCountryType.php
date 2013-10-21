<?php

namespace SO\StandardBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class StdPlaceCountryType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        
        
       $builder->add('name', 'text', array('label' => 'Nom', 'attr' => array('placeholder' => "Nom", 'class' => '_70')));
       
        
       $builder->add('code', 'text', array('label' => 'Code', 'attr' => array('placeholder' => "Code", 'class' => '_70')));
       $builder->add('save', 'submit', array('label' => 'Enregistrer'));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'SO\StandardBundle\Entity\StdPlaceCountry',
            'cascade_validation' => true,
        ));
    }

    public function getName() {
        return 'country';
    }
    

}
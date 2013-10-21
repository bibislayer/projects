<?php

namespace SO\StandardBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class StdDiplomaLevelType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        
        
       $builder->add('name', 'text', array('label' => 'Nom', 'attr' => array('placeholder' => "Nom", 'class' => '_70')));
       $builder->add('save', 'submit', array('label' => 'Enregistrer','attr'=>array('class'=>'btn btn-primary _20_imp')));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'SO\StandardBundle\Entity\StdDiplomaLevel',
            'cascade_validation' => true,
        ));
    }

    public function getName() {
        return 'diploma_level';
    }
    

}
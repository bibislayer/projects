<?php

namespace SO\StandardBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class StdSchoolTypeType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        
       $builder->add('name', 'text', array('label' => 'Nom', 'attr' => array('placeholder' => "Nom", 'class' => '_70')));
       $builder->add('text_introduction', 'textarea', array(
                    'label' => 'Description Courte',
                    'attr' => array('class' => 'tiny_mce _70','rows'=>14)
            )
        );
        $builder->add('text_description', 'textarea', array(
                    'label' => 'Description',
                    'attr' => array('class' => 'tiny_mce _70','rows'=>14)
            )
        );
        $builder->add('save', 'submit', array('label' => 'Enregistrer'));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'SO\StandardBundle\Entity\StdSchoolType',
            'cascade_validation' => true,
        ));
    }

    public function getName() {
        return 'school_type';
    }
    

}
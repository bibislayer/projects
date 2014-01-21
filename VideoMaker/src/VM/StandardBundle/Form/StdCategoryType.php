<?php

namespace VM\StandardBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;


class StdCategoryType extends AbstractType {
    
    private $doctrine;

    public function __construct(RegistryInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }
    public function buildForm(FormBuilderInterface $builder, array $options) {
        
        $builder->add('name', 'text')        
                ->add('parent_id', 'hidden', array('required' => false,'mapped'=>false))
                ->add('presentation', 'textarea', array('attr' => array('class' => 'tiny_mce _70','rows'=>14)))
                ->add('introduction', 'textarea', array('attr' => array('class' => 'tiny_mce _70','rows'=>14)));
       
        $builder->add('save', 'submit', array('label' => 'Enregistrer','attr'=>array('class'=>'btn btn-primary _20_imp')));   
       
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'VM\StandardBundle\Entity\StdCategory',
            'cascade_validation' => true,
        ));
    }

    public function getName() {
        return 'category';
    }
    

}
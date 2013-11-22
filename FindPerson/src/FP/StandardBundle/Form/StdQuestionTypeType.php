<?php

namespace FP\StandardBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;


class StdQuestionTypeType extends AbstractType {
    
    private $doctrine;

    public function __construct(RegistryInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }
    public function buildForm(FormBuilderInterface $builder, array $options) {
        
        $builder->add('name', 'text',array('label' => 'Nom'))
                ->add('template', 'text',array('label' => 'Template'))
                ->add('Help', 'entity',array('label' => 'Help','class'=>'FP\QuestionnaireBundle\Entity\Help'));        
        
       
        $builder->add('save', 'submit', array('label' => 'Enregistrer','attr'=>array('class'=>'btn btn-primary _20_imp')));   
       
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'FP\StandardBundle\Entity\StdQuestionType',
            'cascade_validation' => true,
        ));
    }

    public function getName() {
        return 'std_question_type';
    }
    

}
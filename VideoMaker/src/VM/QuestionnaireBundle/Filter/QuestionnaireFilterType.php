<?php

namespace VM\QuestionnaireBundle\Filter;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class QuestionnaireFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface  $builder, array $options)
    {        
      
      $builder->add('by_enterprise', 'autocomplete', array(
        'url' => array('route' => 'bo_ac_enterprises'),
        'mapped' => false
        )
      );

      $builder->add('by_type', 'entity', array(
            'class' => 'VMStandardBundle:StdQuestionnaireType',
            'property' => 'name',
            'empty_value' => 'Sélectionner',
            'label' => 'Type de questionnaire',
            'attr' => array('class' => '_70'),'required'=>false
            )
      );

      $builder->add('by_status', 'choice', array(
            'choices' => array('published'=>'Publié','closed'=>'Cloturé','creation'=>'En cours de création','need_validate'=>'En attente de validation','refused'=>'Refusé'),
            'empty_value' => 'Sélectionner',
            'label' => 'Statut',
            'attr' => array('class' => '_70'),'required'=>false
            )
      );

      $builder->add('by_has_respondant', 'choice', array(
            'choices' => array('true'=>'Avec répondants','false'=>'Sans répondants'),
            'empty_value' => 'Sélectionner',
            'label' => 'Répondants',
            'attr' => array('class' => '_70'),'required'=>false
            )
      );                            

    }

    
    public function getName()
    {
        return 'questionnaire_filter';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection'   => false,
            'validation_groups' => array('filtering')
        ));
    }
}

?>
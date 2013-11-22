<?php
// SchoolFilterType.php
namespace FP\MailerBundle\Filter;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MailerFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface  $builder, array $options)
    {
        
      $builder->add('by_category', 'entity', array(
            'class' => 'FPMailerBundle:MailerCategory',
            'property' => 'name',
            'empty_value' => 'Sélectionner un category',
            'label' => 'Categorie',
            'attr' => array('class' => '_70'),'required'=>false
            )
        );

      $builder->add('by_category', 'autocomplete', array(
        'url' => array('route' => 'bo_ac_mailer_categories'),
        'label' => 'Categorie de mail',
        'mapped' => false
        )
      );

      $builder->add('by_category2', 'entity', array(
            'class' => 'FPMailerBundle:MailerCategory',
            'property' => 'name',
            'empty_value' => 'Sélectionner un category',
            'label' => 'Categorie',
            'attr' => array('class' => '_70'),'required'=>false
            )
        );

      $builder->add('by_category3', 'entity', array(
            'class' => 'FPMailerBundle:MailerCategory',
            'property' => 'name',
            'empty_value' => 'Sélectionner un category',
            'label' => 'Categorie',
            'attr' => array('class' => '_70'),'required'=>false
            )
        );

      $builder->add('by_category4', 'entity', array(
            'class' => 'FPMailerBundle:MailerCategory',
            'property' => 'name',
            'empty_value' => 'Sélectionner un category',
            'label' => 'Categorie',
            'attr' => array('class' => '_70'),'required'=>false
            )
        );

      $builder->add('by_category5', 'entity', array(
            'class' => 'FPMailerBundle:MailerCategory',
            'property' => 'name',
            'empty_value' => 'Sélectionner un category',
            'label' => 'Categorie',
            'attr' => array('class' => '_70'),'required'=>false
            )
        );
                             

    }

    
    public function getName()
    {
        return 'mailer_filter';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection'   => false,
            'validation_groups' => array('filtering') // avoid NotBlank() constraint-related message
        ));
    }
}

?>
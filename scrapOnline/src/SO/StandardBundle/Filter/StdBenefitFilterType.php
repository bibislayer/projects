<?php
// StdBenefitFilterType.php
namespace SO\StandardBundle\Filter;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Lexik\Bundle\FormFilterBundle\Filter\Query\QueryInterface;

class StdBenefitFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface  $builder, array $options)
    {
    }

    public function getName()
    {
        return 'benefit_filter';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection'   => false,
            'validation_groups' => array('filtering') // avoid NotBlank() constraint-related message
        ));
    }
}
<?php
// StdArticleTypeFilterType.php
namespace SO\StandardBundle\Filter;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Lexik\Bundle\FormFilterBundle\Filter\Query\QueryInterface;

class StdArticleTypeFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface  $builder, array $options)
    {
        
    }

    public function getName()
    {
        return 'article_type_filter';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection'   => false,
            'validation_groups' => array('filtering') // avoid NotBlank() constraint-related message
        ));
    }
}
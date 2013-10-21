<?php
// StdDiplomaTypeFilterType.php
namespace SO\StandardBundle\Filter;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Lexik\Bundle\FormFilterBundle\Filter\Query\QueryInterface;

class StdDiplomaTypeFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface  $builder, array $options)
    {
        $status = array(''=>'Tous','published'=>'Publi&eacute;','need_validate'=>'En attente','editor'=>'En cours de r&eacute;daction','respEditor'=>'En cours de validation r&eacute;daction','unpublished'=>'Non Publi&eacute;');
	$builder->add('by_status', 'choice',array('choices' => $status,'label' => 'Status','required'=>FALSE));
    }

    public function getName()
    {
        return 'diploma_type_filter';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection'   => false,
            'validation_groups' => array('filtering') // avoid NotBlank() constraint-related message
        ));
    }
}
<?php
// CategoryFilterType.php
namespace SO\StandardBundle\Filter;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Lexik\Bundle\FormFilterBundle\Filter\Query\QueryInterface;

class CategoryFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface  $builder, array $options)
    {
      
         /** Filter tag by name status **/
        $status = array(''=>'Tous','published'=>'Publi&eacute;','need_validate'=>'En attente de validation','redac'=>'En cours de r&eacute;daction',
            'resp_redac'=>'En attente de validation r&eacute;daction','unpublished'=>'Non Publi&eacute;');
        $builder->add('by_status', 'choice',array('choices' => $status,'label' => 'Status','required'=>FALSE));
                
        
        
    }

    public function getName()
    {
        return 'category_filter';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection'   => false,
            'validation_groups' => array('filtering') // avoid NotBlank() constraint-related message
        ));
    }
}
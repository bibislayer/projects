<?php 
// src/SO/StandardBundle/Form/Type/NestedSelectType.php
namespace SO\StandardBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

class NestedSelectType extends AbstractType
{          
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    { 
        $resolver->setDefaults(array(
            'config' => array()       
        ));
    }
    
    public function buildView(FormView $view, FormInterface $form, array $options){
        $view->vars['config'] = $options['config'];
    }
            
    public function getParent()
    {
        return 'choice';
    }

    public function getName()
    {
        return 'nested_select';
    }
}

?>
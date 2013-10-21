<?php 
// src/SO/StandardBundle/Form/Type/tinyMCEType.php
namespace SO\StandardBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

class tinyMCEType extends AbstractType
{   
       
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    { 
        $resolver->setDefaults(array(
            'config' => array(
                   'height'=>'400px','width'=>'800px' 
             )       
        ));
    }
    
    public function buildView(FormView $view, FormInterface $form, array $options){
        $view->vars['config'] = $options['config'];
    }
            
    public function getParent()
    {
        return 'textarea';
    }

    public function getName()
    {
        return 'tinyMCE';
    }
}

?>
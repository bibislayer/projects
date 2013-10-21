<?php
namespace SO\StandardBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AutocompleteType extends AbstractType
{
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if (false === $options['autocomplete']) {
            $options['autocomplete'] = 'off';
        }

        // It doesn't hurt even if it will be left empty.
        if (empty($view->vars['attr'])) {
            $view->vars['attr'] = array();
        }
        
        $view->vars['config'] = $options['config'];
        
        if (null !== $options['autocomplete']) {
            $view->vars['attr'] = array_merge(array(
                'autocomplete' => $options['autocomplete'],
                'x-autocompletetype' => $options['autocomplete'],
            ), $view->vars['attr']);
        }
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'autocomplete' => null,
            'config' => array(),
        ));
    }
    
    public function getParent()
    {
        return 'text';
    }

    public function getName()
    {
        return 'autocomplete';
    }
}
?>
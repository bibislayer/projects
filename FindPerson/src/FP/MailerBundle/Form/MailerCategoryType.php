<?php

namespace FP\MailerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MailerCategoryType extends AbstractType {
    
    private $builder = '';
   
    public function buildForm(FormBuilderInterface $builder, array $options) {          
        $builder->add('name' , null);
        $builder->add('parent_id', 'hidden', array('required' => false,'mapped'=>false));
        $builder->add('save', 'submit', array('label' => 'Enregistrer','attr'=>array('class'=>'btn btn-primary _20_imp')));
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'FP\MailerBundle\Entity\MailerCategory',
            'cascade'=> true
        ));        
    }
    
    public function getName(){
        return 'mailer_category';
    }
}    
?>
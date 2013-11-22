<?php

namespace FP\MailerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;

class MailerType extends AbstractType {        
    private $builder = '';
   
    private $doctrine;

    public function __construct(RegistryInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {          
        $builder->add('name' , null);
        $builder->add('subject' , null);
        $builder->add('description' , 'textarea');
        $builder->add('content' , 'tinyMCE',array('config'=>array('height'=>'350px','width'=>'700px')));
        $builder->add('template' , null);
             

        $builder->add('MailerCategory', 'entity', array(
                'class' => 'FPMailerBundle:MailerCategory',
                'property' => 'name',
                'empty_value' => 'Sélectionner un type',
                'required' => true
            )
        );
                
       $builder->add('save', 'submit', array('label' => 'Enregistrer','attr'=>array('class'=>'btn btn-primary _20_imp')));   
    }
    
    
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'FP\MailerBundle\Entity\Mailer',
            'cascade'=> true
        ));
    }
    
    public function getName(){
        return 'mailer';
    }
    
    public function getMailerCategoryData(){
        //Get category list of mailer of level 0       
            return $this->doctrine->getRepository('FPMailerBundle:MailerCategory')->getElements(array('by_level'=>0,'by_order'=>array('mode'=>'ASC','field'=>'name')));
    }
}    
?>
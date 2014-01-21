<?php

namespace VM\QuestionnaireBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormError;

class QuestionnaireType extends AbstractType {        
    private $builder = '';
   
    public function buildForm(FormBuilderInterface $builder, array $options) { 
        
          $builder->add('name' , null);          
          if(isset($options['no_enterprise']) && $options['no_enterprise']==1 ){
              
              $builder->addEventListener(FormEvents::POST_SUBMIT, function(FormEvent $event) {
                    
                    $form = $event->getForm();                          
                    if (!$form->get('enterprise')->getData()) {
                        $form->get('enterprise')->addError(new FormError('Please add enterprise')); 
                    }
                    
               });
              
                $builder->add('enterprise', 'autocomplete', array(
                      'url' => array('route' => 'bo_ac_enterprises'),
                      'mapped' => false
                      )
                );          
          }
          
          $builder->add('StdQuestionnaireType', 'entity', array(
                'class' => 'VMStandardBundle:StdQuestionnaireType',
                'property' => 'name',
                'empty_value' => 'Sélectionner un type',
                'required' => true
               )
          );
          
          //For setting formule
          if(isset($options['formule'])){
              $formule =  $options['formule'];
          }else{
               $formule = 1;
          }
          
          $builder->add('formule', 'choice', array(
                'choices' => array('1' => 'Gratuit' , '2' => 'Payant'  ),
                'required'=>false,
                'mapped'=>false,'expanded'=>true,
                'multiple'=>false,'empty_value' => false,
                'data' => $formule
          ));
          
          
          $anonymous = ($builder->getData()->getAnonymous())? $builder->getData()->getAnonymous() :'0';
          $builder->add('anonymous', 'choice', array(
                'choices' => array('1' => 'Oui', '0' => 'Non'  ),
                'required'=>false,
                'expanded'=>true,
                'empty_value' => false,
                'data' => $anonymous
          ));
          
          $builder->add('text_payment','textarea',array('required'=>false) );
          $builder->add('payment_amount_before',null,array('required'=>false) );
          
          $builder->add('payment_amount_after',null,array('required'=>false) );
          $builder->add('payment_vat','choice',
                        array('choices' => array('19.6' => '19.6%', '5.5' => '5.5' , '7'=>'7'),
                             'required'=>false,
                             'expanded'=>false,
                             'multiple'=>false,'empty_value' => false, 
                        )
          );
          
          $builder->add('text_introduction','tinyMCE', array('config' => array('height' => '50px')));
          $builder->add('text_presentation','tinyMCE', array('config' => array('height' => '250px')));
          
          $builder->add('mail_invitation','textarea');
          $builder->add('mail_accepted','textarea');
          $builder->add('mail_refused','textarea');
          
          $builder->add('save', 'submit', array('label' => 'Enregistrer votre questionnaire','attr'=>array('class'=>'btn_principal')));
    }
    
    
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'VM\QuestionnaireBundle\Entity\Questionnaire',
            'cascade'=> true,
            'formule'=>1,
            'no_enterprise'=>0
        ));
    }
    
    public function getName(){
        return 'questionnaire';
    }
  
}    
?>

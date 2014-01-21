<?php

namespace VM\QuestionnaireBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormError;

class QuestionnaireUserType extends AbstractType {        
    private $builder = '';   
    private $user;
    private $questionnaire;
    
    public function __construct($user , $questionnaire) {
        $this->user = $user;
        $this->questionnaire = $questionnaire;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options) {  
          if($this->user){
              $email = $this->user->getEmail();
              $lastname = '';
              $firstname = '';
              $phonenumber = '';
              if($this->user->getUserProfile()){
                  $lastname = $this->user->getUserProfile()->getLastname();
                  $firstname = $this->user->getUserProfile()->getFirstname();
                  $phonenumber = $this->user->getUserProfile()->getTelephoneFixe();
              }
              
          }else{
              $email = '';
              $firstname = '';
              $lastname = '';
              $phonenumber = '';
          }
          
          $builder->add('email' , null, array('data' => $email));
         
          //If questionnaire is not anonymous
          if(!$this->questionnaire->getAnonymous()){
                $builder->addEventListener(FormEvents::POST_SUBMIT, function(FormEvent $event) {
                    
                    $form = $event->getForm();                   
                    $profile = $form->getData();
                    
                    if ($profile->getFirstname()=='') {
                        $form->get('first_name')->addError(new FormError('Cette valeur ne doit pas être vide.')); 
                    }
                    if ($profile->getLastname()=='') {
                        $form->get('last_name')->addError(new FormError('Cette valeur ne doit pas être vide.')); 
                    }
                   
               });
               
               $builder->add('first_name',null , array('data' => $firstname));
               $builder->add('last_name' ,null, array('data' => $lastname));
          }
          
          
          $builder->add('phone_number' ,null, array('data' => $phonenumber));
          
          $builder->add('save', 'submit', array('label' => 'questionnaire.actionButton.nextStep', 'translation_domain' => 'front','attr'=>array('class'=>'btn_principal')));
    }
    
    
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'VM\QuestionnaireBundle\Entity\QuestionnaireUser',
            'cascade'=> true
        ));
    }
    
    public function getName(){
        return 'questionnaireUser';
    }
  
}    
?>

<?php

namespace VM\QuestionnaireBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class QuestionnaireExportType extends AbstractType {        
    private $builder = '';
   
    public function buildForm(FormBuilderInterface $builder, array $options) {           
       $builder->add('enterprise', 'autocomplete', array(
                'url' => array('route' => 'bo_ac_enterprises'),
                'mapped' => false
                )
        );    
       
       $builder->add('save', 'submit', array('label' => 'Enregistrer votre questionnaire','attr'=>array('class'=>'btn_principal')));
      }
    
    public function getName(){
        return 'QuestionnaireExport';
    }
  
}    
?>

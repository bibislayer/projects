<?php

namespace VM\QuestionnaireBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class QuestionnaireElementType extends AbstractType {        
    private $builder = '';
    private $params;
    
    public function __construct($params=array()) {
         $this->params = $params;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options) {
                 
          $builder->add('name' , null); 
          $builder->add('StdQuestionnaireTypeElement','entity',array(
                'class' => 'VMStandardBundle:StdQuestionnaireTypeElement',
                'property' => 'name',
                'empty_value' => 'Sélectionner un questionnaire element type',               
                'required' => true,'mapped'=>true              
          ));
          
          $builder->add('parent_id', 'hidden', array('required' => false,'mapped'=>false));
          
          $builder->add('time_limit', null , array('required' => false,'mapped'=>false));
          
          if(!empty($this->params)&& array_key_exists('allow_media',$this->params)){
              $allow = $this->params['allow_media'];
          }else{
              $allow = 0;
          }
          
          $builder->add('media_allow', 'choice', array(
                'choices' => array('1' => 'Oui', '0' => 'Non'  ),
                'required'=>false,
                'expanded'=>true,
                'empty_value' => false,
                'data' => $allow,
                'attr'=>array('class'=>'media_allow_element')
          ));
          
          $builder->add('media_embed', 'textarea' , array('required' => false,'mapped'=>false));
          
          if(!empty($this->params)&& array_key_exists('type_media',$this->params)){
              $type_media = $this->params['type_media'];
          }else{
              $type_media = '';
          }
          
          $builder->add('media_type','choice',
                        array('choices' => array('image' => 'Picture', 'video' => 'Video' , 'embed'=>'Embed'),
                             'expanded'=>false,
                             'multiple'=>false,'empty_value' => "Select Media",'data'=>$type_media, 
                             'attr'=>array('class'=>'media_element')
                        )
          );
          
          $builder->add('element_media_file', 'file' , array('required' => false,'mapped'=>false,'attr'=>array('onclick'=>'return uploadFile(this);' ,'data-file_place'=>'attachment_file')));
          
          $builder->add('text_description','tinyMCE',array('config'=>array('height'=>"300px;")));
          
          $builder->add('save', 'submit', array('label' => 'Submit','attr'=>array('class'=>'btn btn-primary _20_imp')));   
    }
    
    
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'VM\QuestionnaireBundle\Entity\QuestionnaireElement',
            'cascade_validation'=> true            
        ));
    }
    
    public function getName(){
        return 'QuestionnaireElement';
    }
  
}    
?>
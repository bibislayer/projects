<?php

namespace VM\QuestionnaireBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use VM\QuestionnaireBundle\Form\QuestionnaireElementType;
use VM\QuestionnaireBundle\Form\QuestionOptionType;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Doctrine\ORM\EntityRepository;

class QuestionType extends AbstractType {

    private $builder = '';
    private $doctrine;

    public function __construct(RegistryInterface $doctrine) {
        $this->doctrine = $doctrine;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $em = $this->doctrine->getEntityManager();

        $params = array('type_media'=>(isset($options['type_media'])?$options['type_media']:''));
        $params['allow_media'] =(isset($options['allow_media'])?$options['allow_media']:'');
         
        $builder->add('QuestionnaireElement', new QuestionnaireElementType($params), array(
            'cascade_validation' => true,
        ));
        
        $builder->add('QuestionOption', new QuestionOptionType($this->doctrine, $builder, $options), array(
            'cascade_validation' => false,'mapped'=>false
        ));
        
        //for setting data field dynamically 
        if (array_key_exists('question_type', $options) && $options['question_type'] != '') {
            $data = $em->getReference("VMStandardBundle:StdQuestionType", $options['question_type']->getId());
        } else {
            $data = "";
        }

        $builder->add('StdQuestionType', 'entity', array(
            'class' => 'VMStandardBundle:StdQuestionType',
            'property' => 'name',
            'empty_value' => 'Sélectionner un question type',
            'required' => true,
            'data' => $data,
            'query_builder' => function(EntityRepository $er ) use ( $options ) {
                if (isset($options['webcam_show']) && $options['webcam_show'] == 0) {
                       return $er->createQueryBuilder('sqt')
                                ->where("sqt.template != 'webcam'");
                } else {
                    return $er->createQueryBuilder('sqt');
                }
            }
        ));

        $question_choices = array('1' => 'Choix unique', '2' => 'Choix multiple ', '3' => 'QCM multiple','4'=>'QCM unique');

        //For selection criteria of choice type
        if (array_key_exists('choice_type', $options) && $options['choice_type'] != '') {
            $choice_type = $options['choice_type'];
        } else {
            $choice_type = 1;
        }

        $builder->add('choice_type', 'choice', array('required' => false, 'mapped' => false, 'data' => $choice_type,
            'choices' => $question_choices, 'empty_value' => false, 'expanded' => true,
            'multiple' => false
                )
        );
      
     
        
        //for selection creteria of checkbox for other options
        if (array_key_exists('doc_type', $options) && !empty($options['doc_type'])) {
            $doc_type = $options['doc_type'];
        } else {
            $doc_type = array();
        }

        $builder->add('doc_type', 'choice',
            array('required' => false, 'label' => false, 'mapped' => false,
                  'choices' => array('pdf' => 'PDF', 'doc' => 'DOC' , 'img'=>'Images' ,'txt'=>'Text'
            ),
            'expanded' => true,
            'multiple' => true,
            'data' => $doc_type
          )
        );
        
        $date_choice = array('1' => 'Simple', '2' => 'Range');

        //for setting data field dynamically 
        if (array_key_exists('type_datetime', $options) && $options['type_datetime'] != '') {
            $type_datetime = $options['type_datetime'];
        } else {
            $type_datetime = 1;
        }

        $builder->add('datetime_type', 'choice', array('required' => false, 'mapped' => false, 'data' => $type_datetime,
            'choices' => $date_choice, 'empty_value' => false, 'expanded' => true,
            'multiple' => false
                )
        );

        //for setting data field dynamically 
        if (array_key_exists('format', $options) && $options['format'] != '') {
            $format = $options['format'];
        } else {
            $format = 'default';
        }


        $formats = array('default' => 'Default', 'd-m-y' => 'd-m-y', 'm-y' => 'm-y', 'y' => 'y', 'H:i' => 'H:i');

        $builder->add('format', 'choice', array('required' => false, 'mapped' => false, 'data' => $format,
            'choices' => $formats, 'empty_value' => false, 'expanded' => false,
            'multiple' => false
                )
        );

        
        $evaluation_types = array('1' => 'Stars', '2' => 'Satisfaction', '3' => 'Mark');

        //for setting data field dynamically 
        if (isset($options['evaluation_type']) && $options['evaluation_type'] != '') {
            $evaluation_type = $options['evaluation_type'];
        } else {
            $evaluation_type = 1;
        }

        $builder->add('evaluation_type', 'choice', array('required' => false, 'mapped' => false, 'data' => $evaluation_type,
            'choices' => $evaluation_types, 'empty_value' => false, 'expanded' => true,
            'multiple' => false
                )
        );

        $builder->add('choice_name', null, array('required' => false, 'mapped' => false, 'attr' => array('maxlength' => 255)));

        $builder->add('good_response', 'choice', array('required' => false, 'label' => false, 'mapped' => false, 'choices' => array('1' => ' '),
            'expanded' => true,
            'multiple' => true
                )
        );

        $builder->add('good_response_unique', 'choice', array('required' => false, 'label' => false, 'mapped' => false, 'choices' => array('1' => ' '),
            'expanded' => true,
            'multiple' => false
                )
        );
        $builder->add('ranking', null, array('required' => false, 'mapped' => false));
        $builder->add('total_ranking', null, array('required' => false, 'mapped' => false));
        $labelSubmit = ($builder->getData()->getId())?'Modifier question':'Ajouter question';
        $builder->add('save', 'submit', array('label' => $labelSubmit, 'attr' => array('class' => 'btn btn-primary  ajout_option')));

    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'VM\QuestionnaireBundle\Entity\Question',
            'cascade_validation' => true,
            'question_type' => '',
            'choice_type' => '',
            'type_datetime' => '',
            'evaluation_type' => '',
            'other_options' => array(),
            'format' => 'default',
            'webcam_show' => 1,
            'doc_type' => array(),
            'type_media'=>'',
            'allow_media'=>0
        ));
    }

    public function getName() {
        return 'Question';
    }

}

?>

<?php

namespace VM\QuestionnaireBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use VM\QuestionnaireBundle\Form\QuestionnaireElementType;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Doctrine\ORM\EntityRepository;

class QuestionOptionType extends AbstractType {

    private $builder = '';
    private $doctrine;

    public function __construct(RegistryInterface $doctrine, FormBuilderInterface $builder, array $options) {
        $this->doctrine = $doctrine;
        $this->options = $options;
        $this->builder = $builder;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        
        
        if (isset($this->options['question_type']) &&  $this->options['question_type']!='' &&($this->options['question_type']->getTemplate() == 'webcam')) {
            $builder->add('response_time', null, array(
                    'required' => false, 'mapped' => false, 'attr' => array('maxlength' => 255,'value'=>($this->builder->getData()->getResponseTime())?$this->builder->getData()->getResponseTime():''
                )));
            $builder->add('question_time', null, array(
                    'required' => false, 'mapped' => false, 'attr' => array('maxlength' => 255,'value'=>($this->builder->getData()->getQuestionTime())?$this->builder->getData()->getQuestionTime():''
                )));
            $builder->add('no_time_limit', null, array('mapped' => false));
        }

        $builder->add('rankin', 'choice', array(
            'choices' => array('1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6', '7' => '7', '8' => '8', '9' => '9', '10' => '10'),
            'required' => false,
            'data' => ($this->builder->getData()->getRankin())?$this->builder->getData()->getRankin():5,
            'empty_value' => 'Pondération',
        ));
        
       
        
        if (isset($this->options['question_type']) && $this->options['question_type']!='' &&($this->options['question_type']->getTemplate() == 'open_question')) {

            /*$builder->add('anti_plagiat', 'choice', array('required' => false,
                'label' => false, 'choices' => array( 1 => 'Oui', '' => 'Non'),
                'expanded' => true, 'data' => $this->builder->getData()->getAntiPlagiat()
            ));*/
            $builder->add('char_limit', null, array('required' => false,
                'data' => $this->builder->getData()->getCharLimit(),
                'attr' => array('maxlength' => 255))
            );
        }

        $builder->add('needed', 'choice', array('required' => false,
            'label' => false, 'choices' => array( 1 => 'Oui', '' => 'Non'),
            'expanded' => true, 'data' => $this->builder->getData()->getNeeded()
        ));
        $builder->add('eliminate_question', 'choice', array('required' => false,
            'label' => false, 'choices' => array( 1 => 'Oui', '' => 'Non'),
            'expanded' => true, 'data' => $this->builder->getData()->getEliminateQuestion()
        ));
                
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'VM\QuestionnaireBundle\Entity\Question',
        ));
    }

    public function getName() {
        return 'QuestionOption';
    }

}

?>

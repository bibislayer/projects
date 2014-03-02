<?php

namespace VM\RecordingSessionBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormError;
use Symfony\Bridge\Doctrine\RegistryInterface;

class RecordingSessionKeywordListType extends AbstractType {        
     public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', null, array(
            'required' => false,
            'attr' => array('class' => 'form-control')
        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'VM\RecordingSessionBundle\Entity\RecordingSessionKeywordList',
        ));
    }

    public function getName()
    {
        return 'recording_session_keyword_list';
    }
    
}    
?>

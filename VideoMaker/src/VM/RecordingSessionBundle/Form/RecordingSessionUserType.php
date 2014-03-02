<?php

namespace VM\RecordingSessionBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormError;
use Symfony\Bridge\Doctrine\RegistryInterface;

class RecordingSessionUserType extends AbstractType {        
     public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('email', 'email', array(
            'required' => true,
            'attr' => array('class' => 'form-control')
        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'VM\RecordingSessionBundle\Entity\RecordingSessionUser',
        ));
    }

    public function getName()
    {
        return 'recording_session_user';
    }
    
}    
?>

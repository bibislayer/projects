<?php

/*
 * This file is part of the VMUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace VM\UserBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;
use VM\UserBundle\Form\Type\ProfileFormType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormError;

class RegistrationFormType extends BaseType
{
    private $class;

    /**
     * @param string $class The User class name
     */
    public function __construct($class)
    {
        $this->class = $class;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        parent::buildForm($builder, $options);

        $builder->remove('username');
        
        $builder->add('user_profile', new ProfileFormType(), array(
            'cascade_validation' => true
        ));
          
        /**/$builder->addEventListener(FormEvents::POST_SUBMIT, function(FormEvent $event) {
                $form = $event->getForm();
                $profile = $form->get('user_profile')->getData();
                if ($profile->getFirstname()=='') {
                    $form->get('user_profile')->get('firstname')->addError(new FormError('Please enter firstname')); 
                }
                if ($profile->getLastname()=='') {
                    $form->get('user_profile')->get('lastname')->addError(new FormError('Please enter lastname')); 
                }          
            }
        );
    }
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        
        $resolver->setDefaults(array(
            'data_class' => $this->class,
            'intention'  => 'resetting',
            'request'=>Null
            //'cascade_validation' => true
        ));
    }
    public function getName()
    {
        return 'recrut_online_user_registration';
    }
}

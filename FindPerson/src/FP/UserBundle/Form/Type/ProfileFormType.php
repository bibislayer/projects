<?php

/*
 * This file is part of the FPUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FP\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\Validator\Constraint\UserPassword as OldUserPassword;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;

class ProfileFormType extends AbstractType
{
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /*if (class_exists('Symfony\Component\Security\Core\Validator\Constraints\UserPassword')) {
            $constraint = new UserPassword();
        } else {
            // Symfony 2.1 support with the old constraint class
            $constraint = new OldUserPassword();
        }
        $builder->add('current_password', 'password', array(
            'label' => 'form.current_password',
            'translation_domain' => 'FPUserBundle',
            'mapped' => false,
            'constraints' => $constraint,
        ));*/
        
        $this->buildUserForm($builder, $options);    
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'FP\UserBundle\Entity\UserProfile',
            'intention'  => 'profile'
        ));
    }

    public function getName()
    {
        return 'recrut_online_user_profile';
    }

    /**
     * Builds the embedded form representing the user.
     *
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    protected function buildUserForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname','text',array('label'=>'Prénom'))
            ->add('lastname','text',array('label'=>'Nom'))
            ->add('gender','choice',array('label'=>'Gender','choices'=>array(''=>'Tous','female'=>'Female','male'=>'Male')))
            ->add('birthday','date',array('label'=>'Birthday'))
            ;
    }
}

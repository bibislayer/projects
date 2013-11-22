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

use Symfony\Component\Form\FormBuilderInterface;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;
use FP\UserBundle\Form\Type\ProfileFormType;
use FP\EnterpriseBundle\Form\Type\EnterpriseFormType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\SecurityContext;

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
        if(!empty ($options['is_pro']) && !empty($options['invite_ent'])){
            $builder->add('enterprise', new EnterpriseFormType(), array(
                'cascade_validation' => true,'mapped'=>false
            ));
        }
        /*
        if(!empty ($options['request'])){
            if($options['request']->get('invite_email')!=''){
                $builder->add('invite_email', 'hidden', array('data'=>$options['request']->get('invite_email'),'mapped'=>false));
            }
            if($options['request']->get('confirm_token')!=''){
                $builder->add('confirm_token', 'hidden', array('data'=>$options['request']->get('confirm_token'),'mapped'=>false));
            }
            
        }*/
    }
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->class,
            'intention'  => 'resetting',
            'is_pro'=>NULL,
            'request'=>Null,
            'invite_ent'=>true
        ));
    }
    public function getName()
    {
        return 'recrut_online_user_registration';
    }
}

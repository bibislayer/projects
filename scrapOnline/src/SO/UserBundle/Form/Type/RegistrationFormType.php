<?php

/*
 * This file is part of the SOUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SO\UserBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;
use SO\UserBundle\Form\Type\ProfileFormType;

class RegistrationFormType extends BaseType
{
    

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        
        $builder->add('user_profile', new ProfileFormType(), array(
            'cascade_validation' => true,
        ));
    }


    public function getName()
    {
        return 'recrut_online_user_registration';
    }
}

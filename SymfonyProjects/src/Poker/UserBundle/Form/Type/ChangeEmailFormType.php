<?php

/*
 * This file is part of the PokerUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Poker\UserBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Security\Core\SecurityContext;

class ChangeEmailFormType extends AbstractType
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
       
        /*$builder->add('email', 'email', array(
            'label' => 'form.current_email',
            'translation_domain' => 'PokerUserBundle',
            'mapped' => false
        ));*/
        $builder->add('email_new', 'email');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->class,
            'intention'  => 'change_email'
        ));
    }

    public function getName()
    {
        return 'poker_user_change_email';
    }
}

<?php

namespace VM\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Form type for representing a UserInterface instance by its username string.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
class UserRoleFormType extends AbstractType
{
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $typeArray=array('ROLE_SUPER_ADMIN'=>'Admin','ROLE_SALER'=>'Saler','ROLE_SALES_MANAGER'=>'Sales manager');
        $builder->add('roles','choice', array('choices' => $typeArray,
                                                       'mapped'=>false,
                                                       'empty_value'=>'',
                                                       'constraints' => new NotBlank()));
        $builder->add('save', 'submit', array('label' => 'Enregistrer','attr'=>array('class'=>'btn btn-primary _20_imp')));   
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'VM\UserBundle\Entity\User',
            'coscade'=>true
        ));
    }
    
    /**
     * @see Symfony\Component\Form\FormTypeInterface::getName()
     */
    public function getName()
    {
        return 'recrut_online_user_role';
    }
}

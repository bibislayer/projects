<?php

/*
 * This file is part of the VMMakerBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace VM\MakerBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use VM\MakerBundle\Entity\MakerInvitationAdministrator;

class MakerInvitationAdministratorFormType extends AbstractType
{
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //echo $this->maker;die;
        $builder->add('email','text');
        $builder->add('roles','choice',array('choices'=>array(''=>'Sélectionner','ROLE_ADMIN'=>'Administrateur','ROLE_EDITOR'=>'Créateur','ROLE_MARKER'=>'Correcteur')));
        
        $builder->add('save', 'submit', array('label' => 'Enregistrer','attr'=>array('class'=>'btn _20_imp')));
        
        
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'VM\MakerBundle\Entity\MakerInvitationAdministrator',
            'coscade'=>true
        ));
    }

    public function getName()
    {
        return 'maker_invitation_administrator';
    }

}

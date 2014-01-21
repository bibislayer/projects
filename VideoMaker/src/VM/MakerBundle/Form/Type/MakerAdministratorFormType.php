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

class MakerAdministratorFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('Maker','autocomplete', array('url' => array('route' => 'bo_ac_makers'),'mapped'=>false))
                ->add('roles','hidden',array('data'=>'ROLE_SALES'));
       
        $builder->add('save', 'submit', array('label' => 'Enregistrer','attr'=>array('class'=>'btn _20_imp')));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'VM\MakerBundle\Entity\MakerAdministrator',
            'coscade'=>true
        ));
    }

    public function getName()
    {
        return 'maker_administrator';
    }

}

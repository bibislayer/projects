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
use VM\MakerBundle\Entity\Maker;

class MakerFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name','text')
            ->add('company_name','text')
            ->add('logo','file',array('data_class' => null, 'required' => false))
            ->add('code_naf','text', array('required' => false))
            ->add('code_siret','text', array('required' => false))
            ->add('phone','text')
            ->add('fax','text', array('required' => false))
            ->add('url_site','text', array('required' => false))
            ->add('text_introduction' , 'tinyMCE',array('config'=>array('height'=>'350px','width'=>'700px')))
            ->add('text_presentation' , 'tinyMCE',array('config'=>array('height'=>'350px','width'=>'700px')))     ;
       
        $builder->add('save', 'submit', array('label' => 'Enregistrer','attr'=>array('class'=>'btn btn-primary _20_imp')));   
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'VM\MakerBundle\Entity\Maker',
            'intention'  => 'maker',
            'cascade_validation' => true
        ));
    }

    public function getName()
    {
        return 'maker';
    }

}

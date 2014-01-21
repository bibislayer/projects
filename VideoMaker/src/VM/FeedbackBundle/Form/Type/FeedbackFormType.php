<?php

/*
 * This file is part of the VMFeedbackBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace VM\FeedbackBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FeedbackFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email',null)
            ->add('text','textarea') ;
            //->add('Question','entity', array('class' => 'VM\QuestionnaireBundle\Entity\Question','property'=>'id','empty_value'=>'Tous'))
       
        $builder->add('save', 'submit', array('label' => 'Enregistrer','attr'=>array('class'=>'btn btn-primary _20_imp')));
          
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'VM\FeedbackBundle\Entity\Feedback',
            'intention'  => 'feedback'
        ));
    }

    public function getName()
    {
        return 'feedback';
    }

}

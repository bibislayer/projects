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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\NotBlank;

class MakerNoteFormType extends AbstractType
{
    private $request;
    
    public function __construct(Request $request) {
        $this->request=$request;
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        if(in_array($this->request->get('_route'), array('bo_customer_note_main_new','bo_customer_note_main_create','bo_customer_note_main_edit', 'bo_customer_note_main_update'))){
               $builder->add('note','textarea',array('attr' => array('class' => 'tiny_mce _70','rows'=>14),
                                                    'data'  => (is_object($options['object']) && $options['object']->getId()?$options['object']->getNote():''),
                                                    'constraints' => new NotBlank()));   
        }else{
            
            $typeArray = array(''=>'Sélectionner','appel' => 'Appel', 'rdv' => 'RDV',
                      'email' => 'E-mail', 'signature' => 'Signature', 'autres' => 'Autres');
            
            $builder->add('type_first','choice', array('choices' => $typeArray,
                                                       'mapped'=>false,'data'=>  (is_object($options['object']) && $options['object']->getId()?$options['object']->getType():Null),
                                                       'constraints' => new NotBlank()))
                    ->add('note_first','textarea',array('attr' => array('class' => 'tiny_mce _70','rows'=>14),
                                                    'mapped'=>false,
                                                    'constraints' => new NotBlank(),
                                                    'data'  => (is_object($options['object']) && $options['object']->getId()?$options['object']->getNote():'')
                                                                ))
                    ->add('date_recall_first','datetime',array('input'=>'datetime',
                                                           'data'  => (is_object($options['object']) && $options['object']->getId()?$options['object']->getDateRecall():new \DateTime()) ,
                                                           'empty_value'=>'', 
                                                           'mapped'=>false,
                                                           'constraints' => new NotBlank()))
                    ->add('type_second','choice', array('choices' => $typeArray,
                                                        'mapped'=>false))                            
                    ->add('note_second','textarea',array('attr' => array('class' => 'tiny_mce _70','rows'=>14),
                                                         'mapped'=>false))
                    ->add('date_recall_second','datetime',array('empty_value'=>'', 
                                                            'data'  => new \DateTime() ,
                                                            'mapped'=>false));
            
                    if(is_object($options['object']) && $options['object']->getIsClose() ){   
                        
                        $builder->add('is_close','hidden',array('data'=>1));
                    }else{
                        $builder->add('is_close','choice',array('choices'=>array(1=>'Oui',0=>'Non'),
                                                    'expanded'=>true,
                                                    'data'  => (is_object($options['object']) && $options['object']->getId()?0:Null) ,
                                                    'constraints' => new NotBlank()))
                                ->add('actions','checkbox',array('mapped'=>false));
                    }    
        }
        $builder->add('save', 'submit', array('label' => 'Enregistrer','attr'=>array('class'=>'btn btn-primary _20_imp')));   
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        
        $resolver->setDefaults(array(
            'data_class' => 'VM\MakerBundle\Entity\MakerNote',
            'coscade'=>true,
            'object'=>Null
        ));
    }

    public function getName()
    {
        return 'maker_note';
    }

}

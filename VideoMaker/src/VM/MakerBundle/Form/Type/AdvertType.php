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
use VM\MakerBundle\Entity\Advert;
use Symfony\Component\HttpFoundation\Request;
use VM\MakerBundle\Form\Type\AdvertQuestionnaireType;

class AdvertType extends AbstractType
{
    private $request;
    
    public function __construct(Request $request) {
        $this->request=$request;
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        if($this->request->get('slug_ent')==''){
            $builder->add('Maker','autocomplete', array('url' => array('route' => 'bo_ac_makers'),'mapped' => false,
                'data'=>(is_object($options['entityObj'])?$options['entityObj']->getMaker():NULL)));
        }        
        if($this->request->get('advert_type')){
            $slug_ent = ($builder->getData()->getMaker() ? $builder->getData()->getMaker()->getSlug():$this->request->get('slug_ent'));
            
            $builder
                ->add('name','text')            
                ->add('text_introduction' , 'tinyMCE',array('config'=>array('height'=>'300px','width'=>'700px')))
                ->add('text_description' , 'tinyMCE',array('config'=>array('height'=>'300px','width'=>'700px')))
                ->add('start_date','date',array('empty_value'=>''))
                ->add('period','text')                             
                ->add('type','text',array('label'=>($this->request->get('advert_type')=='formation'?'Type de formation':'Type de contrat')))            
                ->add('wage_cost','text',array('label'=>($this->request->get('advert_type')=='formation'?'Cout de la formation':'Salaire')));

                
                if($slug_ent != ''){
                    $builder->add('AdvertQuestionnaire', 'collection', array(
                        'type' => new AdvertQuestionnaireType(array('slug_ent' => $slug_ent)),
                        'allow_add' => true,
                        'allow_delete' => true,
                        'prototype' => true,
                        'by_reference' => false,
                    ));
                }

                if(is_object($options['entityObj']) && $options['entityObj']->getId()){
                    $builder->add('advert_type','hidden');
                }else{
                    $builder->add('advert_type','hidden',array('attr'=>array('value'=>($this->request->get('advert_type')=='recrutement'?1:2))));
                }
        }else{
            $builder->add('advert_type','choice',array('choices'=>array(1=>'Recrutement',2=>'Formation'),'expanded' => true));
        }

       
        $builder->add('save', 'submit', array('label' => 'Enregistrer','attr'=>array('class'=>'btn btn-primary _20_imp')));   
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'VM\MakerBundle\Entity\Advert',
            'coscade'=>true,
            'entityObj'=>Null
        ));
    }

    public function getName()
    {
        return 'advert';
    }

}

<?php

namespace SO\StandardBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class StdDiplomaTypeType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        
        $builder->add('StartLevel', 'entity',array('class' => 'SOStandardBundle:StdLevelStudy','property' => 'name',
                'label' => 'Nature du type de diplôme',                
                'empty_value'=>'Sélectionner'));
        
        $builder->add('FinalLevel', 'entity',array('class' => 'SOStandardBundle:StdLevelStudy','property' => 'name',
                'label' => 'Accès',                
                'empty_value'=>'Sélectionner'));
        
        $builder->add('DiplomaNature', 'entity',array('class' => 'SOStandardBundle:StdDiplomaNature','property' => 'name',
                'label' => 'Nature du type de diplôme',
                'empty_value'=>'Sélectionner'));
        
        $builder->add('DiplomaEtat', 'entity',array('class' => 'SOStandardBundle:StdDiplomaLevel','property' => 'name',
                'label' => 'Accès',                
                'empty_value'=>'Sélectionner'));
        $builder->add('name', 'text',array('label' => 'Nom du type de diplôme'));
        
        $builder->add('other_name', 'text',array('label' => 'Alias du type de diplôme'));
        /*$builder->add('period', 'number',array('invalid_message'=>'Required'));
        
        $builder->add('period_type', 'number',array('invalid_message'=>'Required'));*/
        $demande_array = array('0'=>'Sélectionner','1'=> 'Très Faible','2' => 'Faible','3'=>'Moyenne','4' => 'Forte','5'=>'Très Forte');	
      
        $builder->add('demand', 'choice',array('choices'=>$demande_array));
        
        $builder->add('text_introduction', 'textarea', array(
                    'label' => 'Introduction',
                    'attr' => array('class' => 'tiny_mce _70','rows'=>14)
            )
        );
        $builder->add('text_presentation', 'textarea', array(
                    'label' => 'Presentation',
                    'attr' => array('class' => 'tiny_mce _70','rows'=>14)
            )
        );
        $builder->add('text_training', 'textarea', array(
                    'label' => 'Training',
                    'attr' => array('class' => 'tiny_mce _70','rows'=>5)
            )
        );
        $builder->add('save', 'submit', array('label' => 'Enregistrer','attr'=>array('class'=>'btn btn-primary _20_imp')));
        $builder->add('published', 'submit', array('label' => 'Enregistrer et Publier','attr'=>array('class'=>'btn btn-success _30_imp')));
       
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'SO\StandardBundle\Entity\StdDiplomaType',
            'cascade_validation' => true,
        ));
    }

    public function getName() {
        return 'diploma_type';
    }
    

}
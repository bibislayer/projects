<?php

namespace SO\StandardBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class StdCategoryType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        
        $builder->add('name', 'text',array('label' => 'Nom du secteur'));
        

        $builder->add('sector_turnover', 'integer',array('label' => 'Chiffre d\'affaires du secteur'));
        $builder->add('sector_turnover_year', 'text',array('label' => 'Année'));
        
        $builder->add('sector_employment', 'integer',array('label' => 'Nombres d\'emplois dans le secteur'));
        $builder->add('sector_employment_year', 'text',array('label' => 'Année'));
        
        $builder->add('sector_enterprises', 'integer',array('label' => 'Nombres d\'entreprises dans le secteur'));
        $builder->add('sector_enterprises_year', 'text',array('label' => 'Année'));
 
        $builder->add('parent_id', 'hidden', array('required' => false,'mapped'=>false));
        $builder->add('presentation', 'textarea', array(
                    'label' => 'Description longue du secteur',
                    'attr' => array('class' => 'tiny_mce _70','rows'=>14)
            )
        );
        $builder->add('introduction', 'textarea', array(
                    'label' => 'Description courte du secteur',
                    'attr' => array('class' => 'tiny_mce _70','rows'=>14)
            )
        );
        $demande_array = array('0' => 'Sélectionner', '1' => 'Très Faible', '2' => 'Faible', '3' => 'Moyenne', '4' => 'Forte', '5' => 'Très Forte');

        $builder->add('demand', 'choice',array('choices' => $demande_array,'label' => 'Demande'));
        $builder->add('save', 'submit', array('label' => 'Enregistrer','attr'=>array('class'=>'btn btn-primary _20_imp')));
        $builder->add('published', 'submit', array('label' => 'Enregistrer et Publier','attr'=>array('class'=>'btn btn-success _30_imp')));

        
       
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'SO\StandardBundle\Entity\StdCategory',
            'cascade_validation' => true,
        ));
    }

    public function getName() {
        return 'std_category';
    }
    

}
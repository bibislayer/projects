<?php

namespace VM\MakerBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class AdvertQuestionnaireType extends AbstractType {

    private $params = array();

    public function __construct($params) {
        $this->params = $params;        
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {

        $slug_ent = $this->params['slug_ent'];

        $builder->add('Questionnaire', 'entity', array(
                'class' => 'VMQuestionnaireBundle:Questionnaire',
                'property' => 'name',
                'empty_value' => 'Sélectionner un questionnaire',
                'required' => true,
                'query_builder' => function(EntityRepository $er ) use ( $slug_ent ) {
                    return $er->createQueryBuilder('q')
                              ->leftJoin('q.Maker', 'e')
                              ->orderBy('q.name', 'ASC')
                              ->where('e.slug = :slug_ent')
                        ->setParameter('slug_ent', $slug_ent);
                }
            )
        );

    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'VM\MakerBundle\Entity\AdvertQuestionnaire',
            'cascade_validation' => true,
        ));
    }  
        
    public function getName() {
        return 'AdvertQuestionnaire';
    }

}    
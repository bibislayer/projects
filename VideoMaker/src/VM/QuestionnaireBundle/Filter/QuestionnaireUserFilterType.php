<?php

namespace VM\QuestionnaireBundle\Filter;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class QuestionnaireUserFilterType extends AbstractType
{
    private $params = array();
    
    public function __construct($params = array()) {
        $this->params = $params;
    }


    public function buildForm(FormBuilderInterface  $builder, array $options)
    {        
      
      $slug_ent = isset($this->params['slug'])?$this->params['slug']:'';
      
      $builder->add('by_questionnaire', 'entity', array(
            'class' => 'VMQuestionnaireBundle:Questionnaire',
            'property' => 'name',
            'empty_value' => 'Sélectionner un questionnaire',
            'query_builder'=>  function(EntityRepository $er) use ($slug_ent){
                  $er = $er->createQueryBuilder('q')
                              ->leftJoin('q.Enterprise', 'e')
                              ->orderBy('q.name', 'ASC');
                    if($slug_ent!=''){
                         return  $er->where('e.slug = :slug_ent')
                        ->setParameter('slug_ent', $slug_ent);
                    }else{
                        return $er;
                    }           
               },
            'attr' => array('class' => '_70'),'required'=>false         
            )
        );
        
      $builder->add('by_status', 'choice', array(
            'choices' => array('waiting'=>'En Attente','accepted'=>'Accepté','refused'=>'Refusé','test'=>'Testeur'),
            'empty_value' => 'Sélectionner',
            'label' => 'Statut',
            'attr' => array('class' => '_70'),'required'=>false
            )
        );

      $builder->add('by_have_score', 'choice', array(
            'choices' => array('non_scored'=>'Non noté','scored'=>'Noté'),
            'empty_value' => 'Sélectionner',
            'label' => 'Notation',
            'attr' => array('class' => '_70'),'required'=>false
            )
        );                            

    }

    
    public function getName()
    {
        return 'questionnaire_user_filter';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection'   => false,
            'validation_groups' => array('filtering')
        ));
    }
}

?>
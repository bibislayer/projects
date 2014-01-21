<?php

namespace VM\QuestionnaireBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use VM\QuestionnaireBundle\Form\QuestionnaireType;
use VM\QuestionnaireBundle\Form\QuestionType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;


class HelpType extends AbstractType {
    
    private $doctrine;
    private $res_questionaire_type;
    private $res_question_type;

    public function __construct(EntityManager $doctrine)
    {
        $this->doctrine = $doctrine;
    }
    public function buildForm(FormBuilderInterface $builder, array $options) {
        
        
        $builder->add('text', 'text'); 
        
        $this->res_question_type=$this->doctrine->createQuery('SELECT sqt FROM VMStandardBundle:StdQuestionType sqt WHERE  sqt.Help=:id')
                ->setParameter('id', $options['dataObj']->getId())->getOneOrNullResult();
        
        $builder->add('std_question_type', 'entity', array(
            'class' => 'VMStandardBundle:StdQuestionType',
            'property' => 'name',
            'mapped'=>false,
            'empty_value'=>'Tous',
            'data'=>$this->res_question_type,
            'query_builder' => function(EntityRepository $er) {
                    $q=$er->createQueryBuilder('qt');
                    $q->leftJoin('qt.Help','h')
                      ->where($q->expr()->isNull('h.id'));
                        if($this->res_question_type)
                           $q->orWhere($q->expr()->eq('qt.id',$this->res_question_type->getId()));
               return $q->orderBy('qt.name', 'ASC');
            }
        ));
                    
        $this->res_questionaire_type=$this->doctrine->createQuery('SELECT sqt FROM VMStandardBundle:StdQuestionnaireType sqt WHERE  sqt.Help=:id')
                ->setParameter('id', $options['dataObj']->getId())->getOneOrNullResult();  
        
        $builder->add('std_questionnaire_type', 'entity', array(
            'class' => 'VM\StandardBundle\Entity\StdQuestionnaireType',
            'property' => 'name',
            'mapped'=>false,
            'empty_value'=>'Tous',
            'data'=> $this->res_questionaire_type,
            'query_builder' => function(EntityRepository $er) {
                $q=$er->createQueryBuilder('qt');
                $q->leftJoin('qt.Help','h')
                  ->where($q->expr()->isNull('h.id'));
                if($this->res_questionaire_type)
                  $q->orWhere($q->expr()->eq('qt.id',$this->res_questionaire_type->getId()));
                return $q->orderBy('qt.name', 'ASC');
            }
        ));
        $builder->add('save', 'submit', array('label' => 'Enregistrer','attr'=>array('class'=>'btn btn-primary _20_imp')));   
       
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'VM\QuestionnaireBundle\Entity\Help',
            'cascade_validation' => true,
            'dataObj'=>Null
        ));
    }

    public function getName() {
        return 'help';
    }
    

}
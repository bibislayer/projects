<?php
// src/Acme/HelloBundle/Form/Type/LocationType.php
namespace SO\StandardBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;

class PeriodType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $arrayTypeDuree = array(''=>'Sélectionner','heure' => 'Heure', 'semaine' => 'Semaine', 'jour' => 'Jour', 'mois' => 'Mois', 'annee' => 'Année');

        $builder->add('period_type', 'choice', array('label' => '','required'=>false,'choices'=> $arrayTypeDuree, 'attr' => array('class' =>'_20_imp')));

        $builder->add('period', null, array('label' => 'Durée','required'=>false, 'attr' => array('class' => '_40_imp')));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'virtual'=> true
        ));

    }

    public function getName()
    {
        return 'period';
    }
}
?>
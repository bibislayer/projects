<?php
namespace FP\GeneralBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Bridge\Doctrine\Form;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\FormView;
use FP\GeneralBundle\Form\FormListener\AutocompleteListener;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class AutocompleteType extends AbstractType
{


    private $om;
    private $container;
    private $url = array();

    /**
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om, ContainerInterface $container)
    {
        $this->om = $om;
        $this->container = $container;

    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if($options['url']['route']){
            $this->url = $this->container->get('router')->generate($options['url']['route'], array());
        }
        if(array_key_exists('inherit_data', $options) && $options['inherit_data'] == true){
            $builder->add('name', 'text', array('property_path' => 'name', 'mapped' => true));
            $builder->add('id', 'hidden', array('property_path' => 'id', 'mapped' => false));

        }else{
            $builder->addEventSubscriber(new AutocompleteListener($this->om, $options));
        }
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['url'] = $this->url;

    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'compound' => true,
            'allowed_values' => 'id',
            'url' => array('route' => '', 'params' => ''),
            'config' => array(
                'select-first' => false
            ),
        ));

    }

    public function getParent()
    {
        return 'text';
    }

    public function getName()
    {
        return 'autocomplete';
    }
}
?>
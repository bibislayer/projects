<?php

namespace VM\MakerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use VM\MakerBundle\Form\Type\MakerFormType;
use VM\MakerBundle\Form\Type\AdvertType;
use VM\MakerBundle\Entity\Maker;
use VM\MakerBundle\Entity\Advert;

class FormController extends Controller {
    
    public function makerFormAction($slug_ent = NULL){
        
        $formConf = $this->get('form_model');
        $formConf->setView('VMMakerBundle:Form:form.html.twig');
        $formConf->setElement('maker');
        $env = $formConf->getEnv();
        $breadcrumbs = $this->get("white_october_breadcrumbs");

        // REDIRECTION PART
        if(in_array($formConf->getAction(), array('edit', 'update'))){
            if($slug_ent){
                $object = $this->get('maker_repository')->getElements(array('by_slug' => $slug_ent, 'action' => 'one'));
                if($object){
                    $formConf->setUrlParams(array('slug_ent' => $object->getSlug()));
                    $formConf->setH1($this->get('translator')->trans('mo.maker.title.edit', array('%ent%' => $object->getName())));

                    if($env == 'bo'){
                        $breadcrumbs->addItem('Entreprises', $this->get("router")->generate("bo_makers"));
                        $breadcrumbs->addItem($object->getName(), $this->get("router")->generate("bo_maker_show", array('slug_ent' => $slug_ent)));
                    }elseif($env == 'mo'){
                        $breadcrumbs->addItem($object->getName(), $this->get("router")->generate("mo_maker_show", array('slug_ent' => $slug_ent)));
                    }

                    $breadcrumbs->addItem('Modifier');
                }else{

                }
            }else{
            }
        }else{
            $object = new Maker();
            $formConf->setH1('Ajouter une entreprise');
            if($formConf->getEnv() == 'bo'){
                $breadcrumbs->addItem('Entreprises', $this->get("router")->generate("bo_makers"));
                $breadcrumbs->addItem('Ajout');
            }
        }
        $formConf->setForm(new MakerFormType($this->getDoctrine()), $object);
        $params=$formConf->getParams();
        
        if($this->get('request')->getMethod() == 'POST'){
            // Make Validation Part
            $params = $this->makerProcessForm($formConf->getForm(), $formConf->getObject(), $params);

            // See if is success or not and redirect to the show page or return page with error
            if (isset($params['url_success'])) {
                if ($this->get('request')->isXmlHttpRequest()) { return new Response($params['url_success']); }
                else { return $this->redirect($params['url_success']); }
            }
        }                
        $params['params']['entity']=$object;
        
        return $this->render($formConf->getTemplate(), $params);
    }

    private function makerProcessForm($form, $obj, $params) {
        
        $user= $this->container->get('fos_user.user_manager')->createUser();
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();  
            if($this->getRequest()->get('logo_hidden')){
                $obj->setLogo($this->getRequest()->get('logo_hidden'));
            } 
            $em->persist($obj);
            $em->flush();
            $maker = $this->get('maker');
            $maker->uploadImage($form['logo'],$obj,$this->getRequest(),$em);
            $params['url_success'] = $this->generateUrl(($params['params']['env'] == 'bo_'?'bo_maker_show':'mo_maker_show'), array('slug_ent' => $obj->getSlug()));
           
        } else {
            $params['errors'] = $form->getErrors();
        }

        return $params;
    }
    
    //Remove maker
    public function removeMakerAction($id) {
        $em = $this->getDoctrine()->getManager();
        //getting object of event 
        $maker = $this->get('maker_repository')->getElements(array('by_id' => $id, 'action' => 'one'));

        //if event exists into database then delete 
        if ($maker) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($maker);
            $em->flush();
        }
        return $this->redirect($this->generateUrl('bo_makers'));
        //return $this->redirect($this->getRequest()->headers->get('referer'));
    }
    
    public function advertFormAction($slug_advert=NULL){
        
        $formConf = $this->get('form_model');
        $formConf->setView('VMMakerBundle:Form:advert_form.html.twig');
        $formConf->setElement('advert');
        $env = $formConf->getEnv();
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        // INITIALIZE ARRAY FOR QUESTIONNAIRE LINKED
        $originalAQ = array();

        // REDIRECTION PART
        if(in_array($formConf->getAction(), array('edit', 'update'))){
            if($slug_advert){
                $object = $this->get('advert_repository')->getElements(array('by_slug' => $slug_advert, 'action' => 'one'));
                if($object){
                    $formConf->setUrlParams(array('slug_advert' => $object->getSlug(),'advert_type' => $this->getRequest()->get('advert_type'),'slug_ent'=>$this->getRequest()->get('slug_ent')));
                    $formConf->setH1('Modifier l\'annonce '.$object->getName());
                    // SET ALL LINKED QUESTIONNAIRE IN AN ARRAY
                    foreach ($object->getAdvertQuestionnaire() as $aq) $originalAQ[$aq->getId()] = $aq;
                    if($env == 'bo'){
                        $breadcrumbs->addItem('Advert', $this->get("router")->generate("bo_adverts"));
                        $breadcrumbs->addItem($object->getName(), $this->get("router")->generate("bo_advert_show", array('slug_advert' => $slug_advert)));
                    }elseif($env == 'mo'){
                        $breadcrumbs->addItem('Advert', $this->get("router")->generate("bo_adverts"));
                        $breadcrumbs->addItem($object->getName(), $this->get("router")->generate("mo_advert_show", array('slug_ent'=>$this->getRequest()->get('slug_ent'),'slug_advert' => $slug_advert)));
                    }

                    $breadcrumbs->addItem('Modifier');
                }else{

                }
            }else{
            }
        }else{
            $object = new Advert();            
            $formConf->setUrlParams(array('advert_type' => $this->getRequest()->get('advert_type'),'slug_ent'=>$this->getRequest()->get('slug_ent')));
            $formConf->setH1('Ajouter une '.($this->getRequest()->get('advert_type')? $this->getRequest()->get('advert_type') : 'annonce'));

            if($formConf->getEnv() == 'bo'){
                $breadcrumbs->addItem('Advert', $this->get("router")->generate("bo_adverts"));
                $breadcrumbs->addItem('Ajout');
            }elseif($env == 'mo'){
                $breadcrumbs->addItem('Annonces', $this->get("router")->generate("mo_adverts", array('slug_ent' => $this->getRequest()->get('slug_ent'))));
                $breadcrumbs->addItem('Nouveau');
            }
        }

        $formConf->setForm(new AdvertType($this->getRequest()), $object,array('entityObj'=>$object));
        $params=$formConf->getParams();
        
        if($this->get('request')->getMethod() == 'POST'){
            // Make Validation Part
            $params['originalAQ'] = $originalAQ;
            $params = $this->advertProcessForm($formConf->getForm(), $formConf->getObject(), $params);

            // See if is success or not and redirect to the show page or return page with error
            if (isset($params['url_success'])) {
                if ($this->get('request')->isXmlHttpRequest()) { return new Response($params['url_success']); }
                else { return $this->redirect($params['url_success']); }
            }
        }                
        $params['params']['entity']=$object;
        
        return $this->render($formConf->getTemplate(), $params);
    }

    private function advertProcessForm($form, $obj, $params) {
        
        $advertForm= $this->getRequest()->get($form->getName());
        if ($form->isValid()) {
            $em=  $this->getDoctrine()->getManager();
            if(array_key_exists('Maker', $advertForm) && array_key_exists('id', $advertForm['Maker']) && !empty($advertForm['Maker']['id'])){
                $maker=$this->get('maker_repository')->getElements(array('by_id'=>$advertForm['Maker']['id'],'action'=>'one'));
                $obj->setMaker($maker);
            }else if($this->getRequest()->get('slug_ent')){
                $maker=$this->get('maker_repository')->getElements(array('by_slug'=>$this->getRequest()->get('slug_ent'),'action'=>'one'));
                $obj->setMaker($maker);
            }

            // QUESTIONNAIRE LINKED MANAGEMENT
            foreach($obj->getAdvertQuestionnaire() as $t){
                if(array_key_exists($t->getId(), $params['originalAQ'])){
                    unset($params['originalAQ'][$t->getId()]);
                }
                $t->setAdvert($obj);
            }
            foreach($params['originalAQ'] as $daq){
                $em->remove($daq);
            }

            $em->persist($obj);
            $em->flush();
            if($params['params']['env'] == 'bo_'){
                $params['url_success'] = $this->generateUrl('bo_advert_show', array('slug_advert' => $obj->getSlug()));  
            }else{                
                $params['url_success'] = $this->generateUrl('mo_advert_show', array('slug_ent'=>$obj->getMaker()->getSlug(), 'slug_advert' => $obj->getSlug())); 
            }                     
        } else {
            $params['errors'] = $form->getErrors();
        }

        return $params;
    }
    
    
    //Remove advert
    public function removeAdvertAction($slug_advert) {
        $em = $this->getDoctrine()->getManager();
        //getting object of event 
        $advert = $this->get('advert_repository')->getElements(array('by_slug' => $slug_advert, 'action' => 'one'));

        //if event exists into database then delete 
        if ($advert) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($advert);
            $em->flush();
        }
        return $this->redirect($this->getRequest()->headers->get('referer'));
    }
    
}

?>
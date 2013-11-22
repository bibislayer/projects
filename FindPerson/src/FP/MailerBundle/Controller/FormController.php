<?php

namespace FP\MailerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use FP\MailerBundle\Form\MailerType;
use FP\MailerBundle\Entity\Mailer;
use FP\MailerBundle\Entity\MailerCategory;
use FP\MailerBundle\Form\MailerCategoryType;
use FP\StandardBundle\Twig\FPExtension;
use Symfony\Component\DependencyInjection\ContainerInterface;

class FormController extends Controller
{  
    public function mailerFormAction()
    {
        $formConf = $this->get('form_model');
        $formConf->setView('FPMailerBundle:Form:mailer_form.html.twig');
        $formConf->setElement('mailer');

        // REDIRECTION PART
        if(in_array($formConf->getAction(), array('edit', 'update'))){
            if($this->get('request')->get('id')){
                $object = $this->get('mailer_repository')->getElements(array('by_id' => $this->get('request')->get('id'), 'action' => 'one'));
                if($object){
                    $formConf->setUrlParams(array('id' => $object->getId()));
                    $formConf->setH1('Modifier le template de mail '.$object->getName());
                }else{

                }
            }else{

            }
        }else{
            $object = new Mailer();
            $formConf->setH1('Ajouter un template de mail');
        }

        $formConf->setForm(new MailerType($this->getDoctrine()), $object);

        if($this->get('request')->getMethod() == 'POST'){
            // Make Validation Part
            $params = array();
            $params = $this->mailerProcessForm($formConf->getForm(), $formConf->getObject(), $params);

            // See if is success or not and redirect to the show page or return page with error
            if (isset($params['url_success'])) {
                if ($this->get('request')->isXmlHttpRequest()) { return new Response($params['url_success']); }
                else { return $this->redirect($params['url_success']); }
            }
        }

        return $this->render($formConf->getTemplate(), $formConf->getParams());
    }

    //function to perform adding and updating a school
    private function mailerProcessForm($form, $obj, $params) {

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($obj);
            $em->flush();

            //if ($params['env'] == 'bo_') {
                $params['url_success'] = $this->generateUrl('bo_mailer_show', array('id' => $obj->getId()));
            //}
        } else {
            $params['errors'] = $form->getErrors();
        }

        return $params;
    }
    public function mailerCategoryFormAction()
    {
        $formConf = $this->get('form_model');
        $formConf->setView('FPMailerBundle:Form:category_form.html.twig');
        $formConf->setElement('mailer_category');

        // REDIRECTION PART
        if(in_array($formConf->getAction(), array('edit', 'update'))){
            if($this->get('request')->get('id')){
                $object = $this->get('mailer_category_repository')->getElements(array('by_id' => $this->get('request')->get('id'), 'action' => 'one'));
                if($object){
                    $formConf->setUrlParams(array('id' => $object->getId()));
                    $formConf->setH1('Modifier la catégorie de mail : '.$object->getName());
                }else{

                }
            }else{

            }
        }else{
            $object = new MailerCategory();
            $formConf->setH1('Ajouter une catégorie de mail');
        }

        $formConf->setForm(new MailerCategoryType($this->getDoctrine()), $object);

        if($this->get('request')->getMethod() == 'POST'){
            // Make Validation Part
            $params = array();
            $params = $this->mailerCategoryProcessForm($formConf->getForm(), $formConf->getObject(), $params);

            // See if is success or not and redirect to the show page or return page with error
            if (isset($params['url_success'])) {
                if ($this->get('request')->isXmlHttpRequest()) { return new Response($params['url_success']); }
                else { return $this->redirect($params['url_success']); }
            }
        }

        return $this->render($formConf->getTemplate(), $formConf->getParams());
    }

    //function to perform adding and updating a school
    private function mailerCategoryProcessForm($form, $obj, $params) {

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($obj);
            $em->flush();

            //if ($params['env'] == 'bo_') {
                $params['url_success'] = $this->generateUrl('bo_mailer_category_show', array('id' => $obj->getId()));
            //}
        } else {
            $params['errors'] = $form->getErrors();
        }

        return $params;
    }

    //Remove mailer category
    public function removeCategoryAction($id) {
        $em = $this->getDoctrine()->getManager();
        $category = $this->get('mailer_category_repository')->getMailerCategory(array('by_id' => $id, 'action' => 'one'));
        if (is_object($category) && $category->getId()) {
            $repo = $em->getRepository('FPMailerBundle:MailerCategory');
            $repo->removeFromTree($category);
            $em->clear(); // clear cached nodes
            return $this->redirect($this->getRequest()->headers->get('referer'));
            exit;
        }
    }

    //Remove mailer template
    public function removeMailerAction($id) {
        $em = $this->getDoctrine()->getManager();
        //getting object of event 
        $mailer = $this->get('mailer_repository')->getMailers(array('by_id' => $id, 'action' => 'one'));

        //if event exists into database then delete 
        if ($mailer) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($mailer);
            $em->flush();
        }

        return $this->redirect($this->getRequest()->headers->get('referer'));
    }

    // for parent category name
    public function parentMailerCategoryAction($id) {
        $category = $this->get('mailer_category_repository')->getMailerCategory(array('by_id' => $id, 'action' => 'one'));

        $lft = $category->getLft();
        $rgt = $category->getRgt();
        $root_id = $category->getRootId();

        //custom repository CategoryRepository
        $parent_name = $this->get('mailer_category_repository')->getMailerCategoryParentNameTree($root_id, $lft, $rgt);
        return new Response($parent_name);
    }

    //For getting subcategories
    public function subMailerCategoryAction($id) {
        if ($id) {
            $category = $this->get('mailer_category_repository')->getMailerCategory(array('by_id' => $id, 'action' => 'one'));

            $lft = $category->getLft();
            $rgt = $category->getRgt();
            $root_id = $category->getRootId();
            $level = $category->getLevel();

            //using service
            $new_cat = $this->get('mailer_category_repository')->getMailerCategoryChildByOneLevel($lft, $rgt, $root_id, $level);

            $child_array = array();
            //making array of subchile with key and name
            if (count($new_cat) > 0) {
                foreach ($new_cat as $cat) {
                    $child_array[$cat->getId() . '_' . $cat->getLevel()] = $cat->getName();
                }
            }

            return new Response(json_encode(array('cat_data' => $child_array, 'count' => count($new_cat))));
        } else {
            return new Response(json_encode(array('cat_data' => array(), 'count' => 0)));
        }
    }

    //For sending Email
    public function sendMailMailerAction($id) {
        $mailer = $this->get('mailer_repository')->getMailers(array('by_id' => $id, 'action' => 'one'));

        $extension = new FPExtension();
        $context = array('first_name'=>'Joni' , 'username'=>'jon.rajput' , 'password'=>'Hello','validation_link'=>'http://forma-search.com');
        $content = $extension->evaluateString(new \Twig_Environment(), $context , $mailer->getContent());
        
        $message = \Swift_Message::newInstance()
                ->setSubject($mailer->getSubject())
                ->setFrom(array('n.adam@forma-search.com'=>'Nicolas Adam'))
                ->setTo('joni.rajput@silverlineit.com')
                ->setContentType('text/html')
                ->setBody($content);
               
       $this->get('mailer')->send($message);  
      exit;
     }
}
?>

<?php

namespace VM\FeedbackBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use VM\FeedbackBundle\Form\Type\FeedbackFormType;
use VM\FeedbackBundle\Entity\Feedback;

class FormController extends Controller {
    
    //resolved feedback
    public function resolvedFeedbackAction($id) {
        $em = $this->getDoctrine()->getManager();
        $feedback = $this->get('feedback_repository')->getElements(array('by_id' => $id, 'action' => 'one'));

        if ($feedback) {
            $feedback->setResolved(1);
            $em->persist($feedback);
            $em->flush();
        }
        return $this->redirect($this->getRequest()->headers->get('referer'));
    }
    //Remove feedback
    public function removeFeedbackAction($id) {
        $em = $this->getDoctrine()->getManager();
        $feedback = $this->get('feedback_repository')->getElements(array('by_id' => $id, 'action' => 'one'));

        if ($feedback) {
            $em->remove($feedback);
            $em->flush();
        }
        return $this->redirect($this->generateUrl('bo_feedbacks'));
    }
    
    public function feedbackFormAction($id=Null){
        $request = $this->getRequest();
        $formConf = $this->get('form_model');
        $formConf->setView('VMFeedbackBundle:Form:form.html.twig');
        $formConf->setElement('feedback');
        $env = $formConf->getEnv();
        $breadcrumbs = $this->get("white_october_breadcrumbs");        
        $url_params = array();

        // REDIRECTION PART
        if(in_array($formConf->getAction(), array('edit', 'update'))){
                $object = $this->get('feedback_repository')->getElements(array('by_id' => $id, 'action' => 'one'));
                if($object){
                    $url_params['id'] = $object->getId();
                    if($this->getUser() && $object->getEmail()==''){
                        $object->setEmail($this->getUser()->getEmail()); 
                    }
                    $formConf->setH1('Modifier l\'feedback '.$object->getText());
                    if($env == 'bo'){
                        $breadcrumbs->addItem($object->getText(), $this->get("router")->generate("bo_feedback_show", array('id' => $id)));
                    }elseif($env == 'mo'){
                        $breadcrumbs->addItem($object->getText(), $this->get("router")->generate("mo_feedback_show", array('id' => $id)));
                    }
                    $breadcrumbs->addItem('Modifier');
                }
        }else{
            $object = new Feedback();
            $formConf->setH1('Ajouter une feedback'); 
            $breadcrumbs->addItem('Ajout'); 
            if($this->getUser()){
                $object->setEmail($this->getUser()->getEmail()); 
            }
        }
        /**
         * Required url parameters & breadc
         */
            $referer = ($request->get('referer')!=''?$request->get('referer'):$request->headers->get('referer'));
            if(!$referer && $env != 'bo'){
                if($env == 'mo')
                    $url = $this->generateUrl ('mo_dashboard');
                else
                    $url = $this->generateUrl ('fo_homepage');
                return $this->redirect ($url);
            }  
            if($env == 'bo'){
                $breadcrumbs->addItem('Feedbacks', $this->get("router")->generate("bo_feedbacks"));
            }else if($env == 'mo'){
                $url_params['slug_ent'] = $request->get('slug_ent');
            }
            $url_params['referer'] = $referer;
            $formConf->setUrlParams($url_params);
        /**
         * End url set params
         */
        
        $formConf->setForm(new FeedbackFormType($this->get('security.context')), $object);
        $params=$formConf->getParams();
        
        if($this->get('request')->getMethod() == 'POST'){
            // Make Validation Part
            $params = $this->feedbackProcessForm($formConf->getForm(), $formConf->getObject(), $params);

            // See if is success or not and redirect to the show page or return page with error
            if (isset($params['url_success'])) {
                if ($this->get('request')->isXmlHttpRequest()) { return new Response($params['url_success']); }
                else { return $this->redirect($params['url_success']); }
            }
        }                        
        return $this->render($formConf->getTemplate(), $params);
    }

    private function feedbackProcessForm($form, $obj, $params) {    
        
        $user = $this->get('security.context')->getToken()->getUser();
        if(!is_object($user)){
            $user = $this->container->get('fos_user.user_manager')->findUserByEmail($obj->getEmail());
        }
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager(); 
            if(is_object($user)){
                if($obj->getEmail() == $user->getEmail())
                $obj->setEmail(Null);
                $obj->setUser($user);
            }            
            $em->persist($obj);
            $em->flush();
             
            if($params['params']['env'] == 'bo_'){
                $params['url_success'] = $this->generateUrl('bo_feedback_show',array('id' => $obj->getId()));
            }else if($this->getRequest()->get('referer')){
                $params['url_success'] = $this->getRequest()->get('referer');
            }
        } else {
            $params['errors'] = $form->getErrors();
        }

        return $params;
    }
    
    public function feedbackCommentFormAction(){
        if($this->getRequest()->get('id')){
            $em = $this->getDoctrine()->getManager();
            $feedback = $em->getRepository('VMFeedbackBundle:Feedback')->find($this->getRequest()->get('id'));
            if($feedback && $this->getRequest()->get('comment')){
                $feedback->setTextComment($this->getRequest()->get('comment'));
                $feedback->setResolved(1);
                $em->persist($feedback);
                $em->flush();
                echo 'ok';
            }
        }
        exit;
    }
    
    public function commentShowAction(){
        if($this->getRequest()->get('id')){
            $em = $this->getDoctrine()->getManager();
            $feedback = $em->getRepository('VMFeedbackBundle:Feedback')->find($this->getRequest()->get('id'));
            if($feedback){
                echo $feedback->getTextComment();
            }
        }
        exit;
    }
    
    public function changeStatusAction(){
        if($this->getRequest()->get('id')){
            $em = $this->getDoctrine()->getManager();
            $feedback = $em->getRepository('VMFeedbackBundle:Feedback')->find($this->getRequest()->get('id'));
            if($feedback && $this->getRequest()->get('status')){
                switch ($this->getRequest()->get('status')){
                    case  'unread':
                        $feedback->setIsRead(0);
                        break;
                }
                $em->persist($feedback);
                $em->flush();
                return $this->redirect($this->getRequest()->headers->get('referer'));
            }
        }else{
            echo "Invalid arguments";exit;
        }
    } 
}

?>
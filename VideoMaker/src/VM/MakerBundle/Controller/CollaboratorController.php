<?php

namespace VM\MakerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use VM\MakerBundle\Entity\MakerInvitationAdministrator;
use VM\MakerBundle\Form\Type\MakerInvitationAdministratorFormType;

class CollaboratorController extends Controller {

    public function dashboardAction()
    {   
        $access = $this->get('session')->get('access_admin');
        $user = $this->get('security.context')->getToken()->getUser();
        $makerAdministrators= $user->getMakerAdministrator();        
        return $this->render('VMMakerBundle:Collaborator/Middle:dashboard.html.twig', array('makerAdministrators' => $makerAdministrators));
    }

    public function moIndexAction() {
        
        $access = $this->get('session')->get('access_admin');
        $maker = $access['current'];
        $paginate = $this->get("index_paginate"); 
        $paginate->setH1($this->get('translator')->trans('mo.collaborator.title.index', array('%ent%' => $maker['name'])));
        $paginate->setView('VMMakerBundle:Collaborator/Middle:index.html.twig');
        $paginate->setAddNew('mo_collaborator_new','',array('slug_ent'=>$this->get('request')->get('slug_ent')));
        $paginate->addQueryParams(array('by_maker_id'=> $maker['id'],'by_role'=>'ROLE_COLLAB'));//'by_user_id'=>$user->getId()
        $paginate->setQuery($this->get('collaborator_repository')->getElements($paginate->getParamsForQuery()));

        // BREADCRUMBS
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem($maker['name'], $this->get("router")->generate("mo_maker_show", array('slug_ent' => $maker['slug'])));
        $breadcrumbs->addItem('Collaborateurs');

        return $this->render($paginate->getTemplate(), $paginate->getParams());
             
    }

    public function moShowAction() {
        return $this->render('VMMakerBundle:Collaborator/Middle:show.html.twig', array());
    }
    
    public function collaboratorFormAction($id=Null){
        $formConf = $this->get('form_model');
        $formConf->setView('VMMakerBundle:Collaborator/Form:form.html.twig');
        $formConf->setElement('collaborator');
        $url_params = array('slug_ent'=>  $this->getRequest()->get('slug_ent'));
        if($this->getRequest()->get('for')){
            $url_params['for']=$this->getRequest()->get('for');
        }
        $formConf->setUrlParams($url_params);
        $maker = $this->get('maker_repository')->getElements(array('by_slug' => $this->getRequest()->get('slug_ent'), 'action' => 'one'));
        
        if(!$maker){
            return $this->redirect($this->getRequest()->headers->get('referer'));
        }
        
        // BREADCRUMBS
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem($maker->getName(), $this->get("router")->generate($formConf->getEnv()."_maker_show", array('slug_ent' => $maker->getSlug()) ));
        $breadcrumbs->addItem('Collaborateurs', $this->get("router")->generate($formConf->getEnv()."_collaborators", array('slug_ent' => $maker->getSlug())));
        $breadcrumbs->addItem('Nouveau');


        // REDIRECTION PART
        if(in_array($formConf->getAction(), array('edit', 'update'))){
            if($id){
                $object = $this->get('maker_invitation_administrator_repository')->getElements(array('by_id' => $id, 'action' => 'one'));
                $formConf->setH1('Modifier le collaborateur '.$maker->getName());
            }

        }else{
            $object = new MakerInvitationAdministrator();
            $formConf->setH1($this->get('translator')->trans($formConf->getEnv().'.collaborator.title.new', array('%ent%' => $maker->getName())));
        }
        

        $formConf->setForm(new MakerInvitationAdministratorFormType(), $object);
        $params=$formConf->getParams();
        
        if($this->get('request')->getMethod() == 'POST'){
            $returnParams = $this->makerInvitationAdministratorProcessForm($formConf->getForm(), $formConf->getObject(), $params);

            // See if is success or not and redirect to the show page or return page with error
            if (isset($returnParams['url_success'])) {
                if ($this->get('request')->isXmlHttpRequest()) { return new Response($returnParams['url_success']); }
                else { return $this->redirect($returnParams['url_success']); }
            }
        }        
        $params['params']['entity']=$object;
        $params['params']['maker']=$maker;
        $params['params']= array_merge($params['params'],(!empty($returnParams) && is_array($returnParams)?$returnParams:array()));
        return $this->render($formConf->getTemplate(), $params);
    }

    private function makerInvitationAdministratorProcessForm($form, $obj, $params) {

        $user = $this->container->get('security.context')->getToken()->getUser(); 
        $userManager = $this->container->get('fos_user.user_manager');
        $formData= $this->getRequest()->get($form->getName());        
        $maker = $this->get('maker_repository')->getElements(array('by_slug' => $this->get('request')->get('slug_ent'), 'action' => 'one'));
           
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            if(array_key_exists('email', $formData)){
                $userData= $userManager->findUserByEmail($formData['email']);
                $uniqMaker= $this->get('maker_invitation_administrator_repository')->getElements(array('by_email'=>$formData['email'],'by_maker_id'=>$maker->getId(),'actions'=>'count'));
                
                if(!$uniqMaker){
                    if($userData){                        
                        $obj->setUser($userData);
                        //echo $maker->getId(),$userData->getId();
                        if($this->get('collaborator_repository')->isCollaborator($maker->getId(),$userData->getId())){
                            $params['errors'] = 'Already have collaborator';
                            return $params;
                        }
                    }
                    $obj->setMaker($maker); 
                    if(array_key_exists('roles', $formData)){
                        $obj->setRoles(array($formData['roles']));
                    }
                    $obj->setConfirmationToken(sha1($user->getSalt()+$formData['email']));            
                    $em->persist($obj);
                    $em->flush();
                    
                    $mail_params=array(
                        'to'=>$obj->getEmail(),
                        'template'=>'_customerInvitation',
                        'temp_params'=>array(
                            'confirm_link' => $this->get("router")->generate("user_register", array(), true).'pro?email='.$obj->getEmail().'&confirm_token='.$obj->getConfirmationToken(),
                            'email'=>$obj->getEmail(),
                            'ent_name' => $maker->getName()
                        )
                    );
                    $this->get('my_mailer')->sendMail($mail_params);   
                    if($params['params']['env']=='mo_')
                        $params['url_success'] = $this->generateUrl('mo_collaborators', array('slug_ent' => $maker->getSlug()));
                    else{
                        if($this->getRequest()->get('for')=='customer')
                            $params['url_success'] = $this->generateUrl('bo_customer_show', array('slug_ent' => $maker->getSlug()));
                        else
                            $params['url_success'] = $this->generateUrl('bo_collaborators');
                    }                        
                }else{
                    $params['errors'] = 'Both email & maker are already invited';   
                }
            }
        }else{
            $params['errors'] = $form->getErrors();
        }

        return $params;
    }
    
    public function boIndexAction() {
        $paginate = $this->get("index_paginate"); 
        $paginate->setH1($this->get('translator')->trans('bo.collaborator.title.index'));
        $paginate->setView('VMMakerBundle:Collaborator/Back:index.html.twig');
        
        $paginate->addQueryParams(array('by_role'=>'ROLE_COLLAB'));
        $paginate->setQuery($this->get('collaborator_repository')->getElements($paginate->getParamsForQuery()));


        return $this->render($paginate->getTemplate(), $paginate->getParams());
        return $this->render('VMMakerBundle:Collaborator/Back:index.html.twig', array());
    }

    public function boShowAction() {
        return $this->render('VMMakerBundle:Collaborator/Back:show.html.twig', array());
    }

    public function boDeleteAction() {
        //if user is admin $this->deleteCollaborator();
    }

    public function moDeleteAction() {
        //if user is collaborator and have higher level $this->deleteCollaborator();
    }
 
    //Delete Collaborator
    public function deleteCollaboratorAction($id) {
        $em = $this->getDoctrine()->getManager();
        $collaborator = $this->get('collaborator_repository')->getElements(array('by_id' => $id, 'action' => 'one'));
        
        if ($collaborator) {
            $em->remove($collaborator);
            $em->flush();
        }
        return $this->redirect($this->getRequest()->headers->get('referer'));
    }

    //Remove Invitaion
    public function removeInvitationAction($id){
       $em = $this->getDoctrine()->getManager();
        $invitation = $this->get('maker_invitation_administrator_repository')->getElements(array('by_id' => $id, 'action' => 'one'));
        
        if ($invitation) {
            $em->remove($invitation);
            $em->flush();
        }
        return $this->redirect($this->getRequest()->headers->get('referer'));
    }
    
   public function changeRoleAction()  {
       $id = $this->getRequest()->get('id');
       $role = $this->getRequest()->get('role');
       $collabObj = $this->get('collaborator_repository')->getElements(array('by_id' => $id, 'action' => 'one'));
       if($collabObj){
           $collabObj->setRoles(array($role));
           $em = $this->getDoctrine()->getManager();
           $em->persist($collabObj);
           $em->flush();
           echo 'success';
       }else{
           echo 'fail';
       }
       exit;
   }
}

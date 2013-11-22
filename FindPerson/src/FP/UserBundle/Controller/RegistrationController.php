<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FP\UserBundle\Controller;

use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Event\UserEvent;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FP\UserBundle\Entity\UserProfile;
use FP\EnterpriseBundle\Entity\EnterpriseAdministrator;

/**
 * Controller managing the registration
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 * @author Christophe Coevoet <stof@notk.org>
 */
class RegistrationController extends Controller implements ContainerAwareInterface {

    //MEMO//
    /*
     * 3 functions 
     *   -invitation
     *   -register
     *   -registerPro
     */
    public function registerAction(Request $request, $is_pro = NULL) {
        $invitation = NULL;
        $errors = '';
        $invite_ent = TRUE;
        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $this->container->get('fos_user.user_manager');

        $user = $userManager->createUser();
        /** @var $formFactory \FP\UserBundle\Form\Factory\FactoryInterface */
        $formFactory = $this->container->get('recrut_online_user.registration.form.type');
        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->container->get('event_dispatcher');

        $userForm = $request->get($formFactory->getName()); //recrut_online_user_registration

        $em = $this->getDoctrine()->getManager();

        /**
         * SET invited member to collaborator
         */

        if ($request->get('email')) {                     
           $user->setEmail($request->get('email'));
           $invitation = $this->invitationAction($user, $request);   
           $invite_ent = FALSE;
           //$request->getSession()->invalidate();
           $userData= $userManager->findUserByEmail($request->get('email'));
           
           if(!$userData){
               $user->setEmail($request->get('email'));
               $invite_ent=false;
           }
            if ($request->get('confirm_token')) {
                $invitation =$this->get('enterprise_invitation_administrator_repository')->getElements(array('by_email'=>$request->get('email'),'by_confirm_token'=>$request->get('confirm_token'),'action'=>'one'));
            }
            if (!$invitation && !$userData) {
                return $this->redirect($this->generateUrl('user_register').'pro'); 
            }
            if ($userData && $invitation && $entId = $invitation->getEnterprise()->getId()) {
                $objCollaborateur = new EnterpriseAdministrator();
                $objCollaborateur->setUser($userData);
                $objCollaborateur->setEnterprise($invitation->getEnterprise());
                $em->persist($objCollaborateur);
                $em->remove($invitation);
                $em->flush();
                
                return $this->redirect($this->generateUrl('login_admin')); 
            }
            
        } else {
            $invitation = '';
        }      
        
        $user->setEnabled(false);   
        
        $token = sha1(uniqid(mt_rand(), true)); // Or whatever you prefer to generate a token
        $user->setConfirmationToken($token);

        if (is_array($userForm) && array_key_exists('user_profile', $userForm)) {
            $profile = new UserProfile();
            if (array_key_exists('firstname', $userForm['user_profile']) && $userForm['user_profile']['firstname'] != '') {
                $profile->setFirstname($userForm['user_profile']['firstname']);
            } else {
                $errors.='Please enter user firstname<br>';
            }
            if (array_key_exists('lastname', $userForm['user_profile']) && $userForm['user_profile']['lastname'] != '') {
                $profile->setLastname($userForm['user_profile']['lastname']);
            } else {
                $errors.='Please enter user lastname<br>';
            }
            $username = $userForm['user_profile']['firstname'] . '.' . $userForm['user_profile']['lastname'];

            ///SECTION A REVOIR/// 
            $q = $em->createQuery("SELECT MAX(u.username) as username FROM FP\UserBundle\Entity\User u where u.username like '" . $username . "%'");
            $em->flush();
            $result = $q->getResult();
            if (!empty($result[0]['username'])) {
                preg_match('/([\d]+)/', $result[0]['username'], $match);
                $increase = (!empty($match[0]) ? $match[0] + 1 : 1);
                $user->setUsername($username . $increase);
            } else {
                $user->setUsername($username);
            }
            ///FIN SECTION///
            $profile->setUser($user);
            $user->setUserProfile($profile);
            if ($is_pro) {
                $user->setRoles(array('ROLE_MANAGER'));
            }
        }

        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_INITIALIZE, new UserEvent($user, $request));
        $user_class = $this->container->getParameter('fos_user.model.user.class');

        $form = $this->createForm($formFactory, $user, array('is_pro' => $is_pro, 'request' => $request, 'invite_ent' => $invite_ent));
        $form->setData($user);

        if ('POST' === $request->getMethod()) {
            if (is_array($userForm) && array_key_exists('enterprise', $userForm) || ($is_pro && $invite_ent)) {
                if (empty($userForm['enterprise']['name'])) {
                    $errors.='Please enter enterprise name.';
                }
            }
            $form->handleRequest($request);
            if ($form->isValid() && empty($errors)) {

                $userManager->updateUser($user);
                /**
                 * Set parameter for sending email to confirm user account
                 * Call mailer service for sending email
                 */
                $emailParams=array(
                    'to'=>$user->getEmail(),
                    'from'=>array('n.adam@forma-search.com'=>'Nicolas Adam'),
                    'template'=>'confirmUserCreation',
                    'temp_params'=>array(
                        'host'=>  'http://'.$this->getRequest()->getHttpHost(),
                        'email'=>$user->getEmail(),
                        'confirm_token'=>$user->getConfirmationToken(),
                    )
                );
                $this->get('my_mailer')->sendMail($emailParams);                
                // End of the mail service

                $userManager->updateUser($user);
                $paramsView = array();
                $event = new FormEvent($form, $request);
                $urlConfirmation = $this->container->get('router')->generate('user_confirm', array('token' => $user->getConfirmationToken()), true);
                $dispatcher->dispatch(FOSUserEvents::REGISTRATION_SUCCESS, $event);
                if (null === $response = $event->getResponse()) {
                    $url = $this->container->get('router')->generate('user_confirmed');
                    $response = new RedirectResponse($url);
                }
                if (!$is_pro):
                    $params = array(
                        'to' => $user->getEmail(),
                        'from' => array('contact@forma-search.com' => 'FP'),
                        'template' => 'inscription',
                        'temp_params' => array(
                            'confirmationUrl' => $urlConfirmation
                        )
                    );
                else:
                    $params = array(
                        'to' => $user->getEmail(),
                        'from' => array('contact@forma-search.com' => 'FP'),
                        'template' => 'inscriptionPro',
                        'temp_params' => array(
                            'confirmationUrl' => $urlConfirmation
                        )
                    );
                    if ($invitation) {
                        $userForm['enterpriseObj'] = $invitation['objEnterprise'];
                        $this->invitationAction($user, $request, 1);
                        $params = $invitation['paramsMail'];
                        $enterprise = $invitation['objEnterprise'];
                    }else{
                        $enterprise = $this->get('enterprise');
                        $userForm['form'] = $form;
                        $userForm['request'] = $request;
                        $userForm['em'] = $em;
                        $enterprise = $enterprise->createEnterpriseForUser($user, $userForm);
                    }
                    $paramsView = array('user' => $user, 'enterprise' => $enterprise);
                endif;
                if (!$paramsView)
                    $paramsView = array('user' => $user);
                $mailer = $this->get('my_mailer');
                $mailer->sendMail($params);
                $dispatcher->dispatch(FOSUserEvents::REGISTRATION_COMPLETED, new FilterUserResponseEvent($user, $request, $response));
                if (null === $response = $event->getResponse()) {
                    /* $url = $this->container->get('router')->generate('mo_dashboard');
                      $response = new RedirectResponse($url); */
                    return $this->render('FPUserBundle:Registration:register' . ($is_pro ? '_pro' : '') . '_success.html.twig', $paramsView);
                }
                return $response;
            }
        }
        return $this->container->get('templating')->renderResponse('FPUserBundle:Registration:register' . ($is_pro ? '_pro' : '') . '.html.' . $this->getEngine(), array(
                    'form' => $form->createView(), 'errors' => $errors
        ));
    }

    public function registerProAction(Request $request) {
        return $this->registerAction($request, 1);
    }

    public function invitationAction($user, Request $request, $inForm = false) {
        if ($request->get('confirm_token')) {
            $invitation = $this->get('enterprise_invitation_administrator_repository')->getElements(array('by_email' => $request->get('email'), 'by_confirm_token' => $request->get('confirm_token'), 'action' => 'one'));
        }
        if (!$invitation) {
            return $this->redirect($this->generateUrl('user_register') . 'pro');
        }
        $params = array();
        $userExist = true;
        $userManager = $this->container->get('fos_user.user_manager');
        $userData = $userManager->findUserByEmail($request->get('email'));
        $em = $this->getDoctrine()->getManager();

        if (!$userData) {
            $userData = $user;
            $userExist = false;
        }
        if ($userExist || $inForm) {
            $objCollaborateur = new EnterpriseAdministrator(); 
            $objCollaborateur->setUser($userData);
            $objCollaborateur->setEnterprise($invitation->getEnterprise());
            $em->persist($objCollaborateur);
            $em->remove($invitation);
            $em->flush();
        }
        $params = array(
            'to' => $userData->getEmail(),
            'from' => array('contact@forma-search.com' => 'FP'),
            'template' => 'confirmationAdministrator'
        );
        if ($userExist) {
            $mailer = $this->get('my_mailer');
            $mailer->sendMail($params);
            return $this->redirect($this->generateUrl('login_admin'));
        }
        return array('objEnterprise' => $invitation->getEnterprise(), 'paramsMail' => $params);
    }

    /**
     * Tell the user to check his email provider
     */
    public function checkEmailAction() {
        $email = $this->container->get('session')->get('fos_user_send_confirmation_email/email');
        $this->container->get('session')->remove('fos_user_send_confirmation_email/email');
        $user = $this->container->get('fos_user.user_manager')->findUserByEmail($email);

        if (null === $user) {
            throw new NotFoundHttpException(sprintf('The user with email "%s" does not exist', $email));
        }

        return $this->container->get('templating')->renderResponse('FPUserBundle:Registration:checkEmail.html.' . $this->getEngine(), array(
                    'user' => $user,
        ));
    }

    /**
     * Receive the confirmation token from user email provider, login the user
     */
    public function confirmAction(Request $request, $token) {
        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $this->container->get('fos_user.user_manager');

        $user = $userManager->findUserByConfirmationToken($token);

        if (null === $user) {
            throw new NotFoundHttpException(sprintf('The user with confirmation token "%s" does not exist', $token));
        }

        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->container->get('event_dispatcher');

        $user->setConfirmationToken(null);
        $user->setEnabled(true);

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_CONFIRM, $event);

        $userManager->updateUser($user);

        if (null === $response = $event->getResponse()) {
            $url = $this->container->get('router')->generate('user_confirmed');
            $response = new RedirectResponse($url);
        }

        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_CONFIRMED, new FilterUserResponseEvent($user, $request, $response));

        return $response;
    }

    /**
     * Tell the user his account is now confirmed
     */
    public function confirmedAction() {
        $user = $this->container->get('security.context')->getToken()->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        return $this->container->get('templating')->renderResponse('FPUserBundle:Registration:confirmed.html.' . $this->getEngine(), array(
                    'user' => $user,
        ));
    }

    protected function getEngine() {
        return $this->container->getParameter('fos_user.template.engine');
    }

}

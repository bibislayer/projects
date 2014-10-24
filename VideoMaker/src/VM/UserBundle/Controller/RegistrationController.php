<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace VM\UserBundle\Controller;

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
use VM\UserBundle\Entity\UserProfile;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Client;
use Symfony\Component\DomCrawler\Form;
use Symfony\Component\DomCrawler\Crawler;
/**
 * Controller managing the registration
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 * @author Christophe Coevoet <stof@notk.org>
 */
class RegistrationController extends Controller implements ContainerAwareInterface {

    public function registerAction(Request $request) {
        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $this->container->get('fos_user.user_manager');

        $user = $userManager->createUser();
        /** @var $formFactory \VM\UserBundle\Form\Factory\FactoryInterface */
        $formFactory = $this->container->get('recrut_online_user.registration.form.type');
        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->container->get('event_dispatcher');

        $userForm = $request->get($formFactory->getName()); //recrut_online_user_registration

        $em = $this->getDoctrine()->getManager();
        $securityContext = $this->get('security.context');

        $user->setEnabled(false);

        $token = sha1(uniqid(mt_rand(), true)); // Or whatever you prefer to generate a token
        $user->setConfirmationToken($token);  

        if (is_array($userForm) && array_key_exists('user_profile', $userForm)) {
            $profile = new UserProfile();
            $username = $userForm['user_profile']['firstname'] . '.' . $userForm['user_profile']['lastname'];

            ///SECTION A REVOIR/// 
            $q = $em->createQuery("SELECT MAX(u.username) as username FROM VM\UserBundle\Entity\User u where u.username like '" . $username . "%'");
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
        }

        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_INITIALIZE, new UserEvent($user, $request));
        $user_class = $this->container->getParameter('fos_user.model.user.class');

        $form = $this->createForm($formFactory, $user, array('request' => $request));
        $form->setData($user);
        $inForm = false;
        if ('POST' === $request->getMethod()) {
                     
            $form->handleRequest($request);
            if ($form->isValid()) {

                $userManager->updateUser($user);
                $paramsView = array();
                $event = new FormEvent($form, $request);
                $urlConfirmation = $this->container->get('router')->generate('user_confirm', array('token' => $user->getConfirmationToken()), true);
                $dispatcher->dispatch(FOSUserEvents::REGISTRATION_SUCCESS, $event);
                if (null === $response = $event->getResponse()) {
                    $url = $this->container->get('router')->generate('user_confirmed');
                    $response = new RedirectResponse($url);
                } 
                $params = array(
                    'to' => $user->getEmail(),
                    'template' => 'inscription',
                    'temp_params' => array(
                        'confirmationUrl' => $urlConfirmation
                    )
                );
                
                if (!$paramsView)
                    $paramsView = array('user' => $user);
                //$this->get('my_mailer')->sendMail($params);
                $dispatcher->dispatch(FOSUserEvents::REGISTRATION_COMPLETED, new FilterUserResponseEvent($user, $request, $response));
                if($inForm){
                    return $this->redirect($this->generateUrl('mo_dashboard'));
                }else{
                    if (null === $response = $event->getResponse()) {                   
                        return $this->render('VMUserBundle:Registration:register_success.html.twig', $paramsView);
                    }
                    return $response;
                }                
            }
        }
        return $this->container->get('templating')->renderResponse('VMUserBundle:Registration:register.html.' . $this->getEngine(), array(
                    'form' => $form->createView()
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
            return array('redirect'=>$this->generateUrl('user_register').'pro');
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
        $params = array(
            'to' => $userData->getEmail(),
            'template' => 'confirmationAdministrator',
            'temp_params'=>array()
        );
        if ($userExist || $inForm) {
            $objCollaborateur = new EnterpriseAdministrator();
            $objCollaborateur->setUser($userData);
            $objCollaborateur->setRoles($invitation->getRoles());
            $objCollaborateur->setEnterprise($invitation->getEnterprise());
            $em->persist($objCollaborateur);
            $em->remove($invitation);
            
            $userData->setEnabled(1);
            $userData->setConfirmationToken(Null); 
            $rolesArray = $userData->getRoles();
            $rolesArray[] = 'ROLE_MANAGER';                     
            $userData->setRoles(array_unique($rolesArray));
            $em->persist($userData);
            $em->flush();     
            
            if($inForm){
                $this->get('auto_login')->autologinAction($userData);
            }
            
            //$this->get('my_mailer')->sendMail($params);          
            return array('redirect'=>$this->generateUrl('mo_dashboard'));
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

        return $this->container->get('templating')->renderResponse('VMUserBundle:Registration:checkEmail.html.' . $this->getEngine(), array(
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

        return $this->container->get('templating')->renderResponse('VMUserBundle:Registration:confirmed.html.' . $this->getEngine(), array(
                    'user' => $user,
        ));
    }

    protected function getEngine() {
        return 'twig';
    }

}

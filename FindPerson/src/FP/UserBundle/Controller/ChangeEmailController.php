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
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use FP\StandardBundle\Twig\FPExtension;

/**
 * Controller managing the email change
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 * @author Christophe Coevoet <stof@notk.org>
 */
class ChangeEmailController extends Controller implements ContainerAwareInterface
{
    /**
     * Change user email
     */
    public function changeEmailAction(Request $request)
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
        
        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->container->get('event_dispatcher');

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::CHANGE_PASSWORD_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }
        /** @var $formFactory \FP\UserBundle\Form\Factory\FactoryInterface */
        //$formFactory = $this->container->get('fos_user.change_email.form.factory');
        $formFactory = $this->container->get('forma_search_user.change_email.form.type');
        $formData=$request->get($formFactory->getName());
        $form = $this->createForm($formFactory);     
        $form->setData($user);
        
        if ($request->isMethod('POST')) {
        
            if(array_key_exists('email_new', $formData) && $user->getEmail()!=$formData['email_new']){
                $user->setConfirmationToken(sha1($user->getSalt()+$user->getPassword()));
                $form->setData($user);
            }
            $form->bind($request);
            if ($form->isValid()) {    
                /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
                $userManager = $this->container->get('fos_user.user_manager');

                $event = new FormEvent($form, $request);
                
                if(array_key_exists('email_new', $formData) && $user->getEmail()==$formData['email_new']){
                    $user->setEmailNew(NULL);
                }else{
                    $profile=$user->getUserProfile();
                    $email_params=array(
                        'from'=>array('n.adam@forma-search.com'=>'Nicolas Adam'),
                        'to'=>$user->getEmail(),
                        'template'=>'changeProfileEmail',
                        'params'=>array(
                            'first_name'=>  is_object($profile)?$profile->getFirstName():'',
                            'link'=>'http://'.$request->getHttpHost().$this->generateUrl('user_reset_email',array('email_new'=> (array_key_exists('email_new', $formData)?$formData['email_new']:''),'token'=>  (is_object($user)?$user->getConfirmationToken():'')))
                        )
                    );
                    $this->sendEmail($email_params);//
                }
                $userManager->updateUser($user);                

                if (null === $response = $event->getResponse()) {
                    $url = $this->container->get('router')->generate('user_profile_show');
                    $response = new RedirectResponse($url);
                }

                return $response;
            }
        }

        return $this->container->get('templating')->renderResponse(
            'FPUserBundle:ChangeEmail:changeEmail.html.'.$this->container->getParameter('fos_user.template.engine'),
            array('form' => $form->createView())
        );
    }
    public function resetEmailAction(Request $request){
        
        $user = $this->container->get('security.context')->getToken()->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->container->get('event_dispatcher');

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::CHANGE_PASSWORD_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }
        
        if($request->get('token') && $request->get('email_new')){                        
            $userToken=$this->getDoctrine()->getManager()->createQuery('SELECT u FROM FPUserBundle:User u WHERE u.email_new = \''.$request->get('email_new').'\'')->getOneOrNullResult();
            
            if (is_object($userToken) && $userToken->getConfirmationToken()==$request->get('token')){
                // Get the object of user form
                $formFactory = $this->container->get('forma_search_user.change_email.form.type');
                $form = $this->createForm($formFactory);            
                $userManager = $this->container->get('fos_user.user_manager');

                $userToken->setEmail($userToken->getEmailNew());
                $userToken->setEmailNew(Null);
                $userToken->setConfirmationToken(Null);
                $form->setData($userToken);
                $userManager->updateUser($userToken);
                // Redirect on profile page
                $event = new FormEvent($form, $request);
                $dispatcher->dispatch(FOSUserEvents::CHANGE_EMAIL_SUCCESS, $event);
                
                if (null === $response = $event->getResponse()) {
                    $url = $this->container->get('router')->generate('user_profile_show');
                    $response = new RedirectResponse($url);
                }

                $dispatcher->dispatch(FOSUserEvents::CHANGE_PASSWORD_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

                return $response;
            }else{
                throw new AccessDeniedException('This user does not have access to this section.');
            }
        }else{
            throw new AccessDeniedException('This user does not have access to this section.');
        }
    }
    // send Email function
     private function sendEmail($params=array()){ 
         if(array_key_exists('from', $params) && array_key_exists('to', $params) && array_key_exists('template', $params)){            
             $mailer = $this->get('mailer_repository')->getMailers(array('by_template' => $params['template'], 'action' => 'one'));
             $extension = new FPExtension();
             if(is_object($mailer)){
                 $content = $extension->evaluateString(new \Twig_Environment(), $params['params'] , $mailer->getContent());
                 $message = \Swift_Message::newInstance()
                        ->setSubject($mailer->getSubject())
                        ->setFrom($params['from'])
                        ->setTo($params['to'])
                        ->setContentType('text/html')
                        ->setBody($content);

                 $this->get('mailer')->send($message); 
                 return true;
             }             
         }else{
             return false;
         }
     }
     
}

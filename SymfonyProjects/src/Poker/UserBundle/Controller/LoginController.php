<?php

namespace Poker\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Core\Event\AuthenticationEvent;
use Symfony\Component\Security\Core\AuthenticationEvents;

class LoginController extends Controller {
    
    protected $container;


     function __construct(ContainerInterface $container = NULL) {
        $this->container = $container;
    }
    
    public function loginAction(Request $request) {
        $securityContext = $this->get('security.context');
        if ($securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            switch (substr($request->attributes->get('_route'), 0, 2)) {
                case 'fo':
                    return $this->redirect($this->generateUrl('fo_homepage'));
                    break;
                case 'mo':
                    return $this->redirect($this->generateUrl('mo_dashboard'));
                    break;
                case 'bo':
                    return $this->redirect($this->generateUrl('bo_dashboard'));
                    break;
                default:
                    return $this->redirect($this->generateUrl('fo_homepage'));
                    break;
            }
        }
        $session = $request->getSession();

        // get the error if any (works with forward and redirect -- see below)
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } elseif (null !== $session && $session->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = '';
        }

        if ($error) {
            // TODO: this is a potential security risk (see http://trac.symfony-project.org/ticket/9523)
            $error = $error->getMessage();
        }

        // last username entered by the user
        $lastUsername = (null === $session) ? '' : $session->get(SecurityContext::LAST_USERNAME);

        $csrfToken = $this->container->has('form.csrf_provider') ? $this->container->get('form.csrf_provider')->generateCsrfToken('authenticate') : null;

        switch (substr($request->attributes->get('_route'), 0, 2)) {
            case 'fo':
                $view = 'PokerUserBundle:Login/Front:login.html.twig';
                break;
            case 'mo':
                $view = 'PokerUserBundle:Login/Middle:login.html.twig';
                break;
            case 'bo':
                $view = 'PokerUserBundle:Login/Back:login.html.twig';
                break;
            default:
                $view = 'PokerUserBundle:Login/Front:login.html.twig';
                break;
        }

        return $this->render($view, array(
                    'last_username' => $lastUsername,
                    'error' => $error,
                    'csrf_token' => $csrfToken,
        ));
    }
    
    
    public function autologinAction($user) {
        if (!$user) {
            return false;;
        }
        $token = new UsernamePasswordToken($user, Null, 'admin', $user->getRoles());  
        $this->container->get('security.context')->setToken($token);
        
    }

}

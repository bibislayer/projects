<?php
/**
 * Created by JetBrains PhpStorm.
 * User: nicolasadam
 * Date: 29/10/13
 * Time: 16:39
 * To change this template use File | Settings | File Templates.
 */
namespace FP\GeneralBundle\Event;

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class MiddleListener
{
    private $context;
    private $container;
    private $accessAdmin;
    private $request;
    private $session;
    private $route;

    public function __construct(ContainerInterface $container, SecurityContextInterface $context)
    {
        $this->context = $context;
        $this->container = $container;
        $this->request = $container->get('request');
        $this->session = $this->request->getSession();
        $this->route = $this->request->attributes->get('_route');
    }

    public function onKernelController(FilterControllerEvent $event)
    {

        if(!$this->route)
            throw new AccessDeniedHttpException('Cette url n\'existe pas');

        if(substr($this->request->getPathInfo(), 0, 6) == '/admin'){
            $this->checkMiddleRestrictions();
        }
    }

    private function cryptAdminAccess($user_id, $slug_ent){
        $key = $user_id.'$cestlebonheur'.$slug_ent;
        $key = sha1($key);
        $key = md5($key);

        return $key;
    }

    /***
     * CHECK ADMIN RESTRICTIONS
     * @throws \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
     */
    private function checkMiddleRestrictions(){

        // INITIALIZE ACCES ADMIN PARAMS
        if($this->session->get('access_admin')){
            $this->accessAdmin = $this->session->get('access_admin');
        }else{
            $this->accessAdmin = array('keys' => array(), 'current' => '');
        }

        // IF USER IS IN ROUTE EXCEPT DASHBOARD
        if(!in_array($this->route, array('mo_dashboard'))){
            $user = $this->context->getToken()->getUser();
            if($this->request->get('slug_ent') == ''){
                throw new AccessDeniedHttpException('Vous n\'êtes pas autorisé à accéder à cette partie');
            }
            $currentKey = $this->cryptAdminAccess($user->getId(), $this->request->get('slug_ent'));
            if(array_key_exists($currentKey, $this->accessAdmin['keys']) && (date_timestamp_get(date_create()) - $this->accessAdmin['keys'][$currentKey] < 600)){
                if($this->accessAdmin['current']['slug'] != $this->request->get('slug_ent')){
                    $this->accessAdmin['current'] = $this->container->get('doctrine')->getManager()->createQuery('SELECT e.id, e.logo, e.name, e.slug FROM FPEnterpriseBundle:Enterprise e WHERE e.slug = :slug')->setParameter('slug', $this->request->get('slug_ent'))->getOneOrNullResult();
                }
            }else{
                $ent = $this->container->get('doctrine')->getManager()->createQuery('SELECT e.id, e.logo, e.name, e.slug FROM FPEnterpriseBundle:Enterprise e WHERE e.slug = :slug')->setParameter('slug', $this->request->get('slug_ent'))->getOneOrNullResult();
                //$ent = $this->container->get('enterprise_repository')->getElements(array('by_slug' => $this->container->get('request')->get('slug_ent'), 'action' => 'one'));
                if(!$ent){
                    throw new AccessDeniedHttpException('Aucun établissement existant');
                }
                if($this->request->get('slug_ent')){
                    if($this->container->get('collaborator_repository')->isCollaboratorSlug($this->request->get('slug_ent'), $user->getId())){
                        $this->accessAdmin['keys'][$this->cryptAdminAccess($user->getId(), $this->request->get('slug_ent'))] = date_timestamp_get(date_create());
                        $this->accessAdmin['current'] = $ent;
                    }else{
                        throw new AccessDeniedHttpException('Vous n\'êtes pas autorisé à accéder à cette partie');
                    }
                }else{
                    throw new AccessDeniedHttpException('Vous n\'êtes pas autorisé à accéder à cette partie');
                }
            }
        }
        $this->session->set('access_admin', $this->accessAdmin);
    }
}
?>
<?php
// src/Acme/AppBundle/EventListener/RequestListener.php
namespace Poker\GeneralBundle\Event;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use phpbrowscap\Browscap;

/**
 * This class will listen for the kernel.request event and add some extra attributes
 * to the request related to the browser.
 */
class RequestListener extends ContainerAware
{

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $bc = new Browscap($this->container->getParameter('kernel.cache_dir'));
        $event->getRequest()->attributes->set('_browser', $bc->getBrowser());
    }

}
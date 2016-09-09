<?php

namespace Pekkis\Phetomane;

use Pimple\Container;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PimpleListener implements EventSubscriberInterface
{

    /**
     * @var Container
     */
    private $container;


    public function __construct(Container $container)
    {
        $this->container = $container;
    }


    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $request->attributes->set('container', $this->container);
    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST => array(array('onKernelRequest', 22)),
        );
    }
}

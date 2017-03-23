<?php

namespace Commty\Simple\Handler;

use Commty\Simple\EventDispatcher\EventSubscriberInterface;
use Commty\Simple\Http\HttpEvent;
use Commty\Simple\Http\ResponseEvent;

/**
 * Class ApplicationEventHandler
 * @package commty\Handler
 */
class ApplicationEventHandler implements EventSubscriberInterface
{

    const ENDAPPLICATION = 'kernel.onKernelResponse';
    const EXCEPTION = 'kernel.onKernelException';

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            self::EXCEPTION => 'onKernelException',
            self::ENDAPPLICATION => 'onKernelResponse'
        ];
    }

    /**
     * @param HttpEvent $event
     * @return mixed
     */
    public function onKernelException(HttpEvent $event)
    {
        $response = call_user_func($event->getErrorCallback(), $event->getException());
        return $this->onKernelResponse(new ResponseEvent($response));
    }

    /**
     * @param ResponseEvent $event
     * @return mixed
     */
    public function onKernelResponse(ResponseEvent $event)
    {
        $response =  $event->getResponse()->headers->set('X-Powered-By', 'Commty Simple framework');
        return $event->getResponse()->send();
    }
}

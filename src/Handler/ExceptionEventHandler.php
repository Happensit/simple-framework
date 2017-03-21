<?php

namespace Commty\Simple\Handler;

use Commty\Simple\EventDispatcher\EventSubscriberInterface;
use Commty\Simple\Http\HttpEvent;

/**
 * Class ExceptionEventHandler
 * @package commty\Handler
 */
class ExceptionEventHandler implements EventSubscriberInterface
{

    const EXCEPTION = 'kernel.onException';

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            self::EXCEPTION => 'onKernelException'
        ];
    }

    /**
     * @param HttpEvent $event
     * @return mixed
     */
    public function onKernelException(HttpEvent $event)
    {
        return call_user_func(
            $event->getErrorCallback(),
            $event->getException()
        );
    }
}

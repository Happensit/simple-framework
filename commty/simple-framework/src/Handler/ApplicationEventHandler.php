<?php

namespace Commty\Simple\Handler;

use Commty\Simple\EventDispatcher\EventSubscriberInterface;
use Commty\Simple\Http\HttpEvent;

/**
 * Class ApplicationEventHandler
 * @package commty\Handler
 */
class ApplicationEventHandler implements EventSubscriberInterface
{

    const ENDAPPLICATION = 'kernel.onEnd';

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            self::ENDAPPLICATION => 'onKernelEnd'
        ];
    }

    /**
     * @param HttpEvent $event
     * @return mixed
     */
    public function onKernelEnd(HttpEvent $event)
    {
        return;
    }
}

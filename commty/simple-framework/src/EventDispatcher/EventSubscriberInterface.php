<?php

namespace Commty\Simple\EventDispatcher;

/**
 * Interface EventSubscriberInterface
 * @package commty\Event\EventDispatcher
 */
interface EventSubscriberInterface
{
    /**
     * @return array
     */
    public static function getSubscribedEvents();
}

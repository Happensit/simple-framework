<?php

namespace Commty\Simple\EventDispatcher;

/**
 * Class EventDispatcher
 * @package commty\Event\EventDispatcher
 */
class EventDispatcher
{
    /**
     * @var array
     */
    private $listeners = [];

    /**
     * @param string $eventName
     * @param Event|null $event
     * @return Event
     */
    public function dispatch($eventName, Event $event = null)
    {
        if (is_null($event)) {
            $event = new Event();
        }

        if ($listeners = $this->getListeners($eventName)) {
            $this->doDispatch($listeners, $eventName, $event);
        }

        return $event;
    }

    /**
     * @param $eventName
     * @param $listener
     */
    public function addListener($eventName, $listener)
    {
        $this->listeners[$eventName][] = $listener;
    }

    /**
     * @param EventSubscriberInterface $subscriber
     */
    public function addSubscriber(EventSubscriberInterface $subscriber)
    {
        foreach ($subscriber::getSubscribedEvents() as $eventName => $params) {
            if (is_string($params)) {
                $this->addListener($eventName, [$subscriber, $params]);
            } else {
                foreach ($params as $listener) {
                    $this->addListener($eventName, [$subscriber, $listener]);
                }
            }
        }
    }

    /**
     * @param null $eventName
     * @return array|mixed
     */
    public function getListeners($eventName = null)
    {
        if ($eventName !== null) {
            if (isset($this->listeners[$eventName])) {
                return $this->listeners[$eventName];
            }
        }

        return [];
    }

    /**
     * Triggers the listeners of an event.
     *
     * @param callable[] $listeners
     * @param string $eventName
     * @param Event $event
     */
    protected function doDispatch($listeners, $eventName, Event $event)
    {
        foreach ($listeners as $listener) {
            call_user_func($listener, $event, $eventName, $this);
        }
    }
}

<?php

namespace Commty\Simple\EventDispatcher;

/**
 * Class Event
 * @package commty\EventDispatcher
 */
class Event
{
    /**
     * @var bool
     */
    private $propagationStopped = false;

    /**
     * @return bool
     */
    public function isPropagationStopped()
    {
        return $this->propagationStopped;
    }

    /**
     * @return bool
     */
    public function stopPropagation()
    {
        return $this->propagationStopped = true;
    }
}

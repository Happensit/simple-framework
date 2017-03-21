<?php

namespace Commty\Simple\Http;

use Commty\Simple\EventDispatcher\Event;

/**
 * Class HttpEvent
 * @package commty\Http
 */
class HttpEvent extends Event
{
    /**
     * @var \Exception
     */
    private $exception;

    /**
     * @var \Closure
     */
    private $errorCallback;

    /**
     * HttpEvent constructor.
     * @param \Exception $exception
     * @param \Closure $errorCallback
     */
    public function __construct(\Exception $exception, \Closure $errorCallback)
    {
        $this->exception = $exception;
        $this->errorCallback = $errorCallback;
    }

    /**
     * @return \Exception ApiExceptionInterface
     */
    public function getException()
    {
        return $this->exception;
    }

    /**
     * @return callable
     */
    public function getErrorCallback()
    {
        return $this->errorCallback;
    }
}

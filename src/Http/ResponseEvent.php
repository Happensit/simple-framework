<?php

namespace Commty\Simple\Http;

use Commty\Simple\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ResponseEvent
 * @package commty\Http
 */
class ResponseEvent extends Event
{
    /**
     * @var Response
     */
    private $response;

    /**
     * HttpEvent constructor.
     * @param Response $response
     */
    public function __construct(Response $response)
    {
        $this->response = $response;
    }

    /**
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }
}

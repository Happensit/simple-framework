<?php

namespace Commty\Simple\Exception;

/**
 * Class NotFoundHttpException
 * @package Commty\Simple\Exception
 */
class NotFoundHttpException extends Exception
{
    /**
     * NotFoundHttpException constructor.
     * @param null $message
     * @param int $statusCode
     */
    public function __construct($message = null, $statusCode = 404)
    {
        parent::__construct($message, $statusCode);
    }
}
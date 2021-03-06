<?php

namespace Commty\Simple\Exception;

/**
 * Class Exception
 * @package commty\Exception
 */
class Exception extends \Exception implements ApiExceptionInterface
{
    /**
     * @var int
     */
    public $statusCode;

    /**
     * Exception constructor.
     * @param null $message
     * @param int $statusCode
     */
    public function __construct($message = null, $statusCode = 500)
    {
        $this->statusCode = $statusCode;

        parent::__construct($message);
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }
}

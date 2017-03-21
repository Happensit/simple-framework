<?php

namespace Commty\Simple\Exception;

/**
 * Class ConfigureApplicationException
 * @package commty\Exception
 */
class ConfigureApplicationException extends \Exception implements ApiExceptionInterface
{
    /**
     * @var int
     */
    private $statusCode;

    /**
     * ConfigureApplicationException constructor.
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

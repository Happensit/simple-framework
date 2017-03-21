<?php

namespace Commty\Simple\Reader;

use Commty\Simple\Exception\ApiExceptionInterface;

/**
 * Class FileReaderException
 * @package commty\Reader
 */
class FileReaderException extends \Exception implements ApiExceptionInterface
{
    /**
     * @var int
     */
    private $statusCode;

    /**
     * FileReaderException constructor.
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

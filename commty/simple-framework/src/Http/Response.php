<?php

namespace Commty\Simple\Http;

/**
 * Class Response
 * @package commty\Http
 */
class Response
{
    /**
     * @var string
     */
    protected $content = '';

    /**
     * @var int
     */
    protected $statusCode;

    /**
     * @var array
     */
    protected $headers = [];

    /**
     * @var array
     */
    public static $statusTexts = [
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        403 => 'Forbidden',
        404 => 'Not Found',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
    ];

    /**
     * Response constructor.
     * @param string $content
     * @param int $statusCode
     * @param array $headers
     */
    public function __construct($content = '', $statusCode = 200, array $headers = [])
    {
        $this->content = $content;
        $this->statusCode = $statusCode;
        $this->headers = $headers;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return header(sprintf('HTTP/1.1 %s %s', $this->getStatusCode(), $this->getStatusText())) .
            header('X-Powered-By: commty Rest framework') .
            $this->buildHeaders() .
            $this->getContent();
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = (string)$content;
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @param array $headers
     */
    public function setHeaders(array $headers)
    {
        $this->headers[] = $headers;
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @param $statusCode
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
    }

    /**
     * @return mixed
     */
    public function getStatusText()
    {
        return self::$statusTexts[$this->getStatusCode()];
    }

    /**
     * Header Builder
     */
    protected function buildHeaders()
    {
        foreach ($this->getHeaders() as $header) {
            header(sprintf("%s: %s\n", $header[0], $header[1]));
        }
    }
}

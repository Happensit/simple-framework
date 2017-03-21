<?php

namespace Commty\Simple\Http;


/**
 * Class Request
 * @package commty\Http
 */
class Request
{

    private $rawBody;
    private $headers;
    private $hostInfo;
    private $cookies;
    private $queryParams;

    public function getMethod()
    {
        if (isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'])) {
            return strtoupper($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE']);
        }

        if (isset($_SERVER['REQUEST_METHOD'])) {
            return strtoupper($_SERVER['REQUEST_METHOD']);
        }

        return 'GET';
    }

    public function getIp()
    {
        return $_SERVER['REMOTE_ADDR'];
    }

    public function getQueryParams()
    {
        if ($this->queryParams === null) {
            return $_GET;
        }

        return $this->queryParams;
    }

    public function getRequestUri()
    {
        return $_SERVER['REQUEST_URI'];
    }

    /**
     * Returns the header collection.
     * The header collection contains incoming HTTP headers.
     * @return HeaderCollection the header collection
     */
    public function getHeaders()
    {
        if ($this->headers === null) {
            $this->headers = new HeaderCollection;
            if (function_exists('getallheaders')) {
                $headers = getallheaders();
            } elseif (function_exists('http_get_request_headers')) {
                $headers = http_get_request_headers();
            } else {
                foreach ($_SERVER as $name => $value) {
                    if (strncmp($name, 'HTTP_', 5) === 0) {
                        $name = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))));
                        $this->headers->add($name, $value);
                    }
                }

                return $this->headers;
            }
            foreach ($headers as $name => $value) {
                $this->headers->add($name, $value);
            }
        }

        return $this->headers;
    }

    /**
     * Returns the raw HTTP request body.
     * @return string the request body
     */
    public function getRawBody()
    {
        if ($this->rawBody === null) {
            $this->rawBody = file_get_contents('php://input');
        }

        return $this->rawBody;
    }

    public function getHostInfo()
    {
        if ($this->hostInfo === null) {

            if (isset($_SERVER['HTTP_HOST'])) {
                $this->hostInfo = 'http://' . $_SERVER['HTTP_HOST'];
            } elseif (isset($_SERVER['SERVER_NAME'])) {
                $this->hostInfo = 'http://' . $_SERVER['SERVER_NAME'];
            }
        }

        return $this->hostInfo;
    }

    public function getCookies()
    {
        if ($this->cookies === null) {
            $this->cookies = new CookieCollection($_COOKIE, [
                'readOnly' => true,
            ]);
        }

        return $this->cookies;
    }

}
<?php

namespace Commty\Simple\Http;

use Commty\Simple\Di\Container;
use Commty\Simple\Exception\BadMethodCallException;
use Commty\Simple\Exception\NotFoundHttpException;

/**
 * Class Router
 * @package commty\Http
 */
class Router
{
    /**
     * @var array
     */
    private $routes = [];

    /**
     * @var
     */
    private $errorCallback;

    /**
     * @param $path
     * @param $action
     * @internal param $callback
     */
    public function get($path, $action)
    {
        $this->add('GET', $path, $action);
    }

    /**
     * Register a route with the application.
     *
     * @param  string  $uri
     * @param  mixed  $action
     */
    public function post($uri, $action)
    {
        $this->add('POST', $uri, $action);
    }

    /**
     * Register a route with the application.
     *
     * @param  string  $uri
     * @param  mixed  $action
     */
    public function put($uri, $action)
    {
        $this->add('PUT', $uri, $action);
    }

    /**
     * Register a route with the application.
     *
     * @param  string  $uri
     * @param  mixed  $action
     */
    public function patch($uri, $action)
    {
        $this->add('PATCH', $uri, $action);
    }

    /**
     * Register a route with the application.
     *
     * @param  string  $uri
     * @param  mixed  $action
     */
    public function delete($uri, $action)
    {
        $this->add('DELETE', $uri, $action);
    }

    /**
     * Register a route with the application.
     *
     * @param  string  $uri
     * @param  mixed  $action
     */
    public function options($uri, $action)
    {
        $this->add('OPTIONS', $uri, $action);
    }

    /**
     * Error handler
     * @param $callback callable
     */
    public function error(callable $callback)
    {
        $this->errorCallback = $callback;
    }

    /**
     * @return mixed
     */
    public function getErrorCallback()
    {
        return $this->errorCallback;
    }

    /**
     * @param $method
     * @param $path
     * @param $callback
     */
    public function add($method, $path, $callback)
    {
        $pattern = sprintf('/^%s([\/]|)$/', str_replace('/', '\/', $path));

        if (is_array($method)) {
            foreach ($method as $verb) {
                $this->routes[$verb][] = ['path' => $pattern, 'callback' => $callback];
            }
        } else {
            $this->routes[$method][] = ['path' => $pattern, 'callback' => $callback];
        }
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws BadMethodCallException
     * @throws NotFoundHttpException
     */
    public function math(Request $request)
    {
        if (array_key_exists($request->getMethod(), $this->routes) === false) {
            throw new BadMethodCallException("Method is not allowed");
        }

        $requestUri = $request->getRequestUri();

        foreach ($this->routes[$request->getMethod()] as $route) {

            if (preg_match($route['path'], $requestUri, $params)) {
                $params = array_slice($params, 1);
                $callback = $route['callback'];

                if (is_callable($callback)) {
                    $result = call_user_func_array($callback, $params);
                } elseif (stripos($callback, '@') !== false) {
                    list($controller, $method) = explode('@', $callback);
                    $controller = Container::getInstance()->getClass($controller);
                    $dependencies = Container::getInstance()->getDependencies(
                        new \ReflectionMethod($controller, $method)
                    );
                    $result = call_user_func_array([$controller, $method], array_merge($dependencies, $params));
                }

                return print $result;
            }
        }

        throw new NotFoundHttpException(sprintf("Route %s Not found", $requestUri));

    }
}

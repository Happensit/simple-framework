<?php

namespace Commty\Simple\Http;

use Commty\Simple\Di\ContainerInterface;
use Commty\Simple\Exception\BadMethodCallException;
use Commty\Simple\Exception\NotFoundHttpException;

/**
 * Class Router
 * @package commty\Http
 */
class Router
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var array
     */
    private $routes = [];

    /**
     * @var
     */
    private $errorCallback;

    /**
     * Router constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param $path
     * @param $action
     * @internal param $callback
     */
    public function get($path, $action)
    {
        $this->add(['GET', 'HEAD'], $path, $action);
    }

    /**
     * Register a route with the application.
     *
     * @param  string $uri
     * @param  mixed $action
     */
    public function post($uri, $action)
    {
        $this->add('POST', $uri, $action);
    }

    /**
     * Register a route with the application.
     *
     * @param  string $uri
     * @param  mixed $action
     */
    public function put($uri, $action)
    {
        $this->add('PUT', $uri, $action);
    }

    /**
     * Register a route with the application.
     *
     * @param  string $uri
     * @param  mixed $action
     */
    public function patch($uri, $action)
    {
        $this->add('PATCH', $uri, $action);
    }

    /**
     * Register a route with the application.
     *
     * @param  string $uri
     * @param  mixed $action
     */
    public function delete($uri, $action)
    {
        $this->add('DELETE', $uri, $action);
    }

    /**
     * Register a route with the application.
     *
     * @param  string $uri
     * @param  mixed $action
     */
    public function options($uri, $action)
    {
        $this->add('OPTIONS', $uri, $action);
    }

    /**
     *
     * $app->router->match(['get', 'post'], '/', function () {
     *   return 'Hello World';
     *   });
     *
     * @param array $methods
     * @param string $uri
     * @param mixed $action
     */
    public function match($methods, $uri, $action)
    {
        $this->add(array_map('strtoupper', (array) $methods), $uri, $action);
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
    public function matchRoute(Request $request)
    {
        list($method, $pathInfo) = $this->parseIncomingRequest($request);

        if (array_key_exists($method, $this->routes) === false) {
            throw new BadMethodCallException("Method is not allowed");
        }

        foreach ($this->routes[$method] as $route) {
            if (preg_match($route['path'], $pathInfo, $params)) {
                $params = array_slice($params, 1);
                $callback = $route['callback'];

                if (is_callable($callback)) {
                    return call_user_func_array($callback, $params);
                } elseif (stripos($callback, '@') !== false) {
                    list($controller, $method) = explode('@', $callback);
                    $controller = $this->container->getClass($controller);
                    $dependencies = $this->container->getDependencies(
                        new \ReflectionMethod($controller, $method)
                    );

                    return call_user_func_array([$controller, $method], array_merge($dependencies, $params));
                }
            }
        }

        throw new NotFoundHttpException(sprintf("Route %s Not found", $pathInfo));
    }


    /**
     * Parse the incoming request and return the method and path info.
     *
     * @param  \Symfony\Component\HttpFoundation\Request $request
     * @return array
     */
    protected function parseIncomingRequest($request)
    {
        return [$request->getMethod(), $request->getPathInfo()];
    }
}

<?php

namespace Commty\Simple;

use Commty\Simple\Di\Container;
use Commty\Simple\EventDispatcher\EventDispatcher;
use Commty\Simple\EventDispatcher\EventSubscriberInterface;
use Commty\Simple\Exception\ApiExceptionInterface;
use Commty\Simple\Exception\ConfigureApplicationException;
use Commty\Simple\Exception\Exception;
use Commty\Simple\Handler\ApplicationEventHandler;
use Commty\Simple\Http\HttpEvent;
use Commty\Simple\Http\Request;
use Commty\Simple\Http\Response;
use Commty\Simple\Http\ResponseEvent;
use Commty\Simple\Http\Router;
use Commty\Simple\ServiceProvider\ServiceProviderInterface;

/**
 * Class Application
 * @package commty
 */
class Application
{
    /**
     * @var
     */
    protected $environment;
    /**
     * @var Container
     */
    public $container;

    /**
     * @var EventDispatcher
     */
    protected $dispatcher;

    /**
     * @var Router
     */
    public $router;

    /**
     * @var string
     */
    protected $basePath;

    /**
     * @var array
     */
    protected $defaultSubscribers = [
        'applicationEventHandler' => ApplicationEventHandler::class
    ];

    /**
     * Application constructor.
     * @param $environment
     * @param array $config
     */
    public function __construct($environment, $basePath = null, $config = [])
    {
        if ($environment === 'prod') {
            set_exception_handler([$this, 'exceptionHandler']);
        }

        $this->basePath = $basePath;
        $this->environment = $environment;
        $this->container = new Container();
        $this->router = new Router($this->container);
        $this->preInit($config);

    }

    /**
     * Register Service Provider
     * @param ServiceProviderInterface $provider
     */
    public function register(ServiceProviderInterface $provider)
    {
        if (method_exists($provider, 'register')) {
            $provider->register($this);
        }
    }

    /**
     * Run Application
     * @param  Request|null  $request
     */
    public function run($request = null)
    {
        if ($request === null) {
            $request = Request::createFromGlobals();
        }

        $this->container->setClass($request);
        $response = $this->router->matchRoute($request);

        if ($response instanceof Response) {
            $response->prepare($request);
            $this->dispatcher->dispatch(
                ApplicationEventHandler::ENDAPPLICATION,
                new ResponseEvent($response)
            );
        } else {
            echo (string)$response;
        }
    }

    /**
     * @param Exception $exception
     */
    public function exceptionHandler($exception)
    {
        if (!$exception instanceof ApiExceptionInterface) {
            $exception = new Exception($exception->getMessage());
        }

        $this->dispatcher->dispatch(
            ApplicationEventHandler::EXCEPTION,
            new HttpEvent($exception, $this->router->getErrorCallback())
        );
    }

    /**
     * @param $config
     * @throws ConfigureApplicationException
     */
    private function preInit($config)
    {
        $config['subscribers'] = !empty($config['subscribers']) ? $config['subscribers'] : [];
        $this->initializeEventSubscribers($config['subscribers']);
        unset($config['subscribers']);

        if (array_key_exists('providers', $config)) {
            foreach ($config['providers'] as $provider) {
                $this->register(new $provider);
            }
            unset($config['providers']);
        }

        foreach ($config as $key => $properties) {
            if (empty($properties['class'])) {
                throw new ConfigureApplicationException(sprintf(
                    "'%s' key doesn't have value 'class' parameter in config file",
                    $key
                ));
            }

            $this->container->setClass($key, $properties);
        }
    }

    /**
     * @param $subscribers array
     */
    protected function initializeEventSubscribers(array $subscribers)
    {
        $this->dispatcher = $this->container->getClass(EventDispatcher::class);
        $subscribers = array_unique(array_merge($this->defaultSubscribers, $subscribers));
        foreach ($subscribers as $key => $subscriber) {
            /** @var $subscriberInstance EventSubscriberInterface */
            $subscriberInstance = $this->container->setClass($key, [$subscriber]);
            $this->dispatcher->addSubscriber($subscriberInstance);
        }
    }

    /**
     * Get the base path for the application.
     * @return string
     */
    public function getBasePath()
    {
        if ($this->basePath === null) {
            $this->basePath = dirname(getcwd());
        }

        return $this->basePath;
    }

    /**
     * Get the storage path for the application.
     *
     * @param  string|null  $path
     * @return string
     */
    public function getStoragePath($path = null)
    {
        return $this->getBasePath().'/storage'.($path ? '/'.$path : $path);
    }
}

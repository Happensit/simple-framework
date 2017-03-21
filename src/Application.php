<?php

namespace Commty\Simple;

use Commty\Simple\Di\Container;
use Commty\Simple\EventDispatcher\EventDispatcher;
use Commty\Simple\EventDispatcher\EventSubscriberInterface;
use Commty\Simple\Exception\ConfigureApplicationException;
use Commty\Simple\Handler\ApplicationEventHandler;
use Commty\Simple\Handler\ExceptionEventHandler;
use Commty\Simple\Http\HttpEvent;
use Commty\Simple\Http\Request;
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
    protected $rootDir;

    /**
     * @var array
     */
    protected $defaultSubscribers = [
        'exceptionEventHandler' => ExceptionEventHandler::class,
        'applicationEventHandler' => ApplicationEventHandler::class
    ];

    /**
     * Application constructor.
     * @param $environment
     * @param array $config
     */
    public function __construct($environment, $config = [])
    {
        if ($environment === 'prod') {
            set_exception_handler([$this, 'exceptionHandler']);
        }

        $this->environment = $environment;
        $this->container = Container::getInstance();
        $this->router = new Router();
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
     */
    public function run()
    {
        $this->router->math(new Request());
    }

    /**
     * @param $exception
     */
    public function exceptionHandler($exception)
    {
        $this->dispatcher->dispatch(
            ExceptionEventHandler::EXCEPTION,
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
                $this->register($provider);
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

            $this->container->set($key, $properties);
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
            $subscriberInstance = $this->container->set($key, [$subscriber]);
            $this->dispatcher->addSubscriber($subscriberInstance);
        }
    }

    /**
     * @return string
     */
    public function getRootDir()
    {
        if (null === $this->rootDir) {
            $this->rootDir = dirname(getcwd());
        }

        return $this->rootDir;
    }
}

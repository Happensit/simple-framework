<?php

namespace Commty\Simple\Di;

/**
 * Interface ContainerInterface
 * @package commty\Container
 */
interface ContainerInterface
{
    /**
     * @param $className
     * @return object
     */
    public function getClass($className);

    /**
     * @return Container
     */
    public static function getInstance();
}

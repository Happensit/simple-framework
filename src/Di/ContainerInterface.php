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
     * @param $instance Object
     * @return object
     */
    public function setClass($className, $instance);

    /**
     * @param $className
     * @return object
     */
    public function getClass($className);

    /**
     * @param $reflection
     * @return array
     */
    public function getDependencies($reflection);
}

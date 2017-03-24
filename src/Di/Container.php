<?php

namespace Commty\Simple\Di;

/**
 * Class Container
 * @package commty\Container
 */
class Container implements ContainerInterface
{

    /**
     * @var array
     */
    protected $classes = [];

    /**
     * @param $className
     * @param array $dependencies
     * @return object
     */
    public function getClass($className, $dependencies = [])
    {
        if (isset($this->classes[$className])) {
            return $this->classes[$className];
        }

        $reflection = new \ReflectionClass($className);
        $dependencies = $this->getDependencies($reflection->getConstructor());
        $object = $reflection->newInstanceArgs($dependencies);

        return $this->classes[$className] = $object;
    }

    /**
     * @param \ReflectionFunctionAbstract $reflection
     * @return array
     */
    public function getDependencies($reflection)
    {
        $dependencies = [];

        if ($reflection instanceof \ReflectionFunctionAbstract) {
            foreach ($reflection->getParameters() as $param) {
                if ($param->isDefaultValueAvailable()) {
                    $dependencies[] = $param->getDefaultValue();
                } else {
                    $class = $param->getClass();
                    if ($class) {
                        $dependencies[] = self::getClass($class->getName());
                    }
                }
            }
        }

        return $dependencies;
    }

    /**
     * @param $class
     * @param array $definition
     * @return object
     */
    public function setClass($class, $definition = [])
    {
        if (is_object($class)) {
            unset($this->classes[get_class($class)]);
            return $this->classes[get_class($class)] = $class;
        }

        if (empty($definition)) {
            return $this->getClass($class);
        }

        return $this->classes[$class] = $this->getClass(array_shift($definition), $definition);
    }
}

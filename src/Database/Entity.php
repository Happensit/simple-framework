<?php

namespace Commty\Simple\Database;

use Commty\Simple\Exception\BadMethodCallException;

/**
 * Class Entity
 * @package commty\Database
 */
abstract class Entity implements EntityInterface
{
    /**
     * @return string
     */
    abstract public function getPersistent();

    /**
     * @return string
     */
    public function getPrimaryKey()
    {
        return 'id';
    }

    /**
     * Magic getter
     * @param $name
     * @return mixed
     * @throws BadMethodCallException
     */
    public function __get($name)
    {
        $getter = 'get' . str_replace('_', '', ucwords($name, '_'));

        if (is_callable([$this, $getter])) {
            return $this->{$getter}();
        }

        throw new BadMethodCallException(sprintf(
            'The option "%s" does not have a callable "%s" getter method which must be defined',
            $name,
            $getter
        ));

    }

    /**
     * Magic setter
     * @param $name
     * @param $value
     * @return mixed
     */
    public function __set($name, $value)
    {
        $setter = 'set' . str_replace('_', '', ucwords($name, '_'));

        if (is_callable([$this, $setter])) {
            $this->{$setter}($value);

            return;
        }

    }
}

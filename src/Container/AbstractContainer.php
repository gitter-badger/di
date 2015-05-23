<?php

namespace Concat\Di\Container;

use OutOfBoundsException;
use BadMethodCallException;
use ArrayAccess;

/**
 * An abstract container which allows for consistent behaviour between
 * all extending containers.
 */
abstract class AbstractContainer implements ContainerInterface, ArrayAccess
{
    /**
     * Returns a list of keys for which dependencies were not provided.
     *
     * @return array list of keys for which dependencies were not provided.
     */
    protected function getMissingDependencies()
    {
        return array_diff_key($this->defaults(), $this->dependencies);
    }

    /**
     * Creates default instances for dependencies that were not provided.
     */
    protected function createDefaults()
    {
        foreach ($this->getMissingDependencies() as $name => $value) {
            if ( ! isset($this->dependencies[$name])) {
                $this->dependencies[$name] = $this->createDefault($value);
            }
        }
    }

    /**
     * Determines and returns a default value's instance object.
     *
     * @return object The default dependency instance
     */
    protected function createDefault($value)
    {
        // A callable is automatically invoked and the return value is used
        if (is_callable($value)) {
            return $value();
        }

        // Objects are returned as is, closures would trigger the is_callable
        if (is_object($value)) {
            return $value;
        }

        // Class name as string assumed, can instantiate the object
        return new $value;
    }

    /**
     * Lazy load the dependency and return its instance.
     *
     * @param string $name The name of the dependency
     *
     * @return object The dependency instance
     */
    protected function load($name)
    {
        if (is_a($this->dependencies[$name], 'Closure')) {
            $this->dependencies[$name] = $this->dependencies[$name]();
        }

        return $this->dependencies[$name];
    }

    /**
     * Returns a dependency instance if one exists.
     *
     * @param string $name The name of the dependency
     *
     * @return mixed The dependency instance
     * @throws OutOfBoundsException
     */
    public function get($name)
    {
        if ( ! isset($this->dependencies[$name])) {
            throw new \OutOfBoundsException("Dependency not found for'$name'");
        }

        return $this->load($name);
    }

    /**
     * Creates an instance of this container using the provided dependencies.
     *
     * @param array|object $dependencies Dependencies to use for this container
     *
     * @return AbstractContainer An instance of the extending container
     */
    public static function make($dependencies = [])
    {
        return new static((array) $dependencies);
    }

    /**
     * Magic method to allow direct object access on the container.
     */
    public function __get($name)
    {
        return $this->get($name);
    }

    /**
     *  Magic method to allow array syntax access on this container
     */
    public function offsetGet($name)
    {
        return $this->get($name);
    }

    /**
     * Returns true if a dependency exists for the given name
     */
    public function offsetExists($name)
    {
        return array_key_exists($name, $this->dependencies);
    }

    /**
     * Containers are immutable so set is not supported.
     *
     * @throws BadMethodCallException
     */
    public function offsetSet($name, $value)
    {
        throw new BadMethodCallException("Not supported");
    }

    /**
     * Containers are immutable so unset is not supported.
     *
     * @throws BadMethodCallException
     */
    public function offsetUnset($name)
    {
        throw new BadMethodCallException("Not supported");
    }
}

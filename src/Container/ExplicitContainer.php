<?php

namespace Concat\Di\Container;

use InvalidArgumentException;

/**
 * A dependency injection container that receives dependencies as an associative
 * array, where the keys are the names of the dependencies and the values are
 * their corresponding instances.
 *
 * A good use case for this container is when you are using an "options" style
 * constructor parameter, for example:
 *
 * public function __construct(array $options)
 * {
 *      $container = Container::make($options);
 *      $this->dependency = $container->dependency;
 * }
 */
abstract class ExplicitContainer extends AbstractContainer
{
    /**
     * {@inheritdoc}
     */
    protected function __construct(array $dependencies)
    {
        parent::__construct($dependencies);

        $this->createDefaults();
        $this->evaluateAll();
        $this->validateAll();
    }

    /**
     * Evaluates any dependencies that were provided as a closure but aren't
     * required to be a closure dependency. Usually this is the case when a
     * dependency instance is generated as a result of a function, rather than
     * instantiated directly.
     */
    private function evaluateAll()
    {
        foreach ($this->dependencies as $name => &$value) {
            $this->evaluate($value, $this->getType($name));
        }
    }

    /**
     * Evaluates a value if it was provided as a closure but isn't required to
     * be a closure dependency.
     */
    private function evaluate(&$value, $type)
    {
        if ($type !== "Closure" && is_a($value, "Closure")) {
            $value = $value();
        }
    }

    /**
     * Validates all dependencies against their expected types.
     *
     * @throws InvalidArgumentException
     */
    private function validateAll()
    {
        foreach ($this->dependencies as $name => $value) {
            $type = $this->getType($name);

            if( ! $this->validate($value, $type)) {
                throw new InvalidArgumentException(
                    "'$name' does not match expected type '$type'"
                );
            }
        }
    }

    /**
     * Validates a provided dependency against its required type.
     *
     * @param object $value The dependency
     * @param string $type The required type of the dependency
     *
     * @return boolean True is the value matches the type, false otherwise
     */
    private function validate($value, $type)
    {
        switch ($type) {
            case 'callable':
                return is_callable($value);
            default:
                return is_a($value, $type);
        }
    }

    /**
     * Returns the required type for a named dependency.
     *
     * @param string $name The name of the dependency
     *
     * @return string The required class name of the dependency
     */
    private function getType($name)
    {
        $types = $this->types();
        return $types[$name];
    }
}

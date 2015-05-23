<?php

namespace Concat\Di\Container;

use InvalidArgumentException;

/**
 * A dependency injection container that receives a list of dependencies which
 * are automatically matched to their names by type.
 *
 * A good use case for this container is when you want to to allow arbitrary
 * constructor injection order.
 *
 * public function __construct()
 * {
 *      $container = Container::make(func_get_args());
 *      $this->dependency = $container->dependency;
 * }
 */
abstract class ImplicitContainer extends AbstractContainer
{
    /**
     * {@inheritdoc}
     */
    protected function __construct(array $dependencies)
    {
        parent::__construct($dependencies);

        $this->resolveAll();
        $this->createDefaults();
    }

    /**
     * Resolves all provided dependencies to their defined names.
     */
    private function resolveAll()
    {
        foreach ($this->dependencies as $dependency) {
            $name = $this->resolve($dependency);
            $this->dependencies[$name] = $dependency;
        }
    }

    /**
     * Resolves a single dependency to its defined name.
     *
     * @param object $dependency The dependency to resolve.
     *
     * @return string|null The name of the dependency of null if unmatched
     * @throws InvalidArgumentException
     */
    private function resolve($dependency)
    {
        if ( ! ($name = $this->getName($dependency))) {
            throw new InvalidArgumentException("Could not resolve dependency");
        }

        return $name;
    }

    /**
     * Returns the name that matches the provided dependency.
     *
     * @param object $dependency The dependecy to match a name to
     *
     * @return string|null The name of the dependency of null if unmatched
     */
    private function getName($dependency)
    {
        // flip the types so we can
        $types = array_flip($this->types());

        if ( ! is_object($dependency)) {
            return $this->resolveNonObject($dependency, $types);
        }

        return $this->resolveObject($dependency, $types);
    }

    /**
     * Attempts to match an object type dependency to a name using an array of
     * types, where the keys are the type and the values are the name.
     *
     * @param object $dependency The dependency to match a name to
     * @param array $types The dependency classes used to determine the match
     *
     * @return string|null The name of the dependency or null if unmatched
     */
    private function resolveObject($dependency, array $types)
    {
        // Resolve closures regardless of potential Closure type definitions
        if (is_a($dependency, "Closure")) {
            $dependency = $dependency();
        }

        $class = get_class($dependency);

        // Check if there is a direct object-type match
        if (isset($types[$class])) {
            return $types[$class];
        }

        // Check if the dependency is a subclass of the type
        foreach ($types as $type => $name) {
            if (is_a($class, $type, true)) {
                return $name;
            }
        }
    }

    /**
     * Attempt to match a non-object type dependency to a name using an array of
     * types, where the keys are the type and the values are the name.
     *
     * @param object $dependency The dependency to match a name to
     * @param array $types The dependency classes used to determine the match
     *
     * @return string|null The name of the dependency or null if unmatched
     *
     */
    private function resolveNonObject($value, $types)
    {
        if (is_callable($value) && isset($types['callable'])) {
            return $types['callable'];
        }
    }
}

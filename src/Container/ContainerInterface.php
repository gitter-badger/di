<?php

namespace Concat\Di\Container;

/**
 * An interface that defines a dependency injection container. It's unlikely
 * that you will use this interface directly, as in most cases you would be
 * extending AbstractContainer instead, which implements this interface.
 */
interface ContainerInterface
{
    /**
     * Returns an associative array where the keys are the names of the
     * dependencies and the values their corresponding class names.
     *
     * For example,
     * [
     *      "handler" => "Some\Handler\Class",
     * ]
     *
     * @return array an array mapping dependency names to expected class names
     */
    function types();

    /**
     * Returns an associative array where the keys are the names of the
     * dependencies and the values their corresponding default values. The
     * value can be an instance, class name, or callable which produces an
     * instance, thereby only instantiating if required.
     *
     * For example,
     * [
     *      "handler" => "Namespace\DefaultInstance",
     *      "another" => new DefaultInstance(),
     * ]
     *
     * @return array an array mapping dependency names to default values
     */
    function defaults();
}

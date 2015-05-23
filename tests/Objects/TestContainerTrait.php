<?php

namespace Concat\Di\Tests\Objects;

trait TestContainerTrait
{
    public static $types;
    public static $defaults;

    public function types()
    {
        return self::$types;
    }

    public function defaults()
    {
        return self::$defaults;
    }
}

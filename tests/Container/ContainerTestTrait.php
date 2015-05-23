<?php

namespace Concat\Di\Tests\Container;

use Concat\Di\Tests\Objects\A;
use Concat\Di\Tests\Objects\B;

trait ContainerTestTrait
{
    public function createTestObject()
    {
        return new A();
    }

    protected function create($instances, $options)
    {
        $container = "\\Concat\\Di\\Tests\\Objects\\" . $this->containerClass;

        $container::$types    = $options['types'];
        $container::$defaults = $options['defaults'];

        return $container::make($instances);
    }

    protected function createBasic($instances = []){
        return $this->create($instances, [
            'types' => [
                'a' => 'Concat\Di\Tests\Objects\A',
                'b' => 'Concat\Di\Tests\Objects\B',
                'c' => 'Concat\Di\Tests\Objects\C',
            ],

            'defaults' => [
                'a' => 'Concat\Di\Tests\Objects\A',
                'b' => 'Concat\Di\Tests\Objects\B',
                'c' => 'Concat\Di\Tests\Objects\C',
            ],
        ]);
    }

    protected function assertBasicTypes($container)
    {
        $this->assertInstanceOf('Concat\Di\Tests\Objects\A', $container->a);
        $this->assertInstanceOf('Concat\Di\Tests\Objects\B', $container->b);
        $this->assertInstanceOf('Concat\Di\Tests\Objects\C', $container->c);

        $this->assertTrue(isset($container['a']));

        $this->assertInstanceOf('Concat\Di\Tests\Objects\A', $container['a']);
        $this->assertInstanceOf('Concat\Di\Tests\Objects\B', $container['b']);
        $this->assertInstanceOf('Concat\Di\Tests\Objects\C', $container['c']);
    }

    protected function _testAllInstancesProvided($instances)
    {
        $container = $this->createBasic($instances);
        $this->assertBasicTypes($container);
    }

    protected function _testOnlyOneInstancesProvided($instances)
    {
        $container = $this->createBasic($instances);
        $this->assertBasicTypes($container);
    }

    protected function _testNoInstancesProvided()
    {
        $container = $this->createBasic();
        $this->assertBasicTypes($container);
    }

    protected function _testSubclassInstance($instances)
    {
        $container = $this->create($instances, [
            'types' => [
                'a' => 'Concat\Di\Tests\Objects\A',
                'b' => 'Concat\Di\Tests\Objects\B',
            ],

            'defaults' => [],
        ]);

        $this->assertInstanceOf('Concat\Di\Tests\Objects\A', $container->a);
        $this->assertInstanceOf('Concat\Di\Tests\Objects\C', $container->b);
    }

    public function testSubclassDefaults()
    {
        $container = $this->create([], [
            'types' => [
                'a' => 'Concat\Di\Tests\Objects\A',
                'b' => 'Concat\Di\Tests\Objects\B',
            ],

            'defaults' => [
                'a' => 'Concat\Di\Tests\Objects\A',
                'b' => 'Concat\Di\Tests\Objects\C',
            ],
        ]);

        $this->assertInstanceOf('Concat\Di\Tests\Objects\A', $container->a);
        $this->assertInstanceOf('Concat\Di\Tests\Objects\C', $container->b);
    }

    public function testInstantiatedDefaults()
    {
        $container = $this->create([], [
            'types' => [
                'a' => 'Concat\Di\Tests\Objects\A',
                'b' => 'Concat\Di\Tests\Objects\B',
            ],

            'defaults' => [
                'a' => new A(),
                'b' => new B(),
            ],
        ]);

        $this->assertInstanceOf('Concat\Di\Tests\Objects\A', $container->a);
        $this->assertInstanceOf('Concat\Di\Tests\Objects\B', $container->b);
    }

    public function testClosureDefaults()
    {
        $container = $this->create([], [
            'types' => [
                'a' => 'Concat\Di\Tests\Objects\A',
                'b' => 'Concat\Di\Tests\Objects\B',
            ],

            'defaults' => [
                'a' => function(){ return new A(); },
                'b' => function(){ return new B(); },
            ],
        ]);

        $this->assertInstanceOf('Concat\Di\Tests\Objects\A', $container->a);
        $this->assertInstanceOf('Concat\Di\Tests\Objects\B', $container->b);
    }

    protected function _testExpectedCallableTypes($instances)
    {
        $container = $this->create($instances, [
            'types' => [
                'a' => 'Concat\Di\Tests\Objects\A',
                'b' => 'callable',
            ],

            'defaults' => [],
        ]);

        $this->assertInstanceOf('Concat\Di\Tests\Objects\A', $container->a);
        $this->assertTrue(is_callable($container->b));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    protected function _testUnexpectedInstance($instances)
    {
        $container = $this->create($instances, [
            'types' => [
                'a' => 'Concat\Di\Tests\Objects\A',
                'b' => 'Concat\Di\Tests\Objects\B',
            ],

            'defaults' => [],
        ]);

        $this->assertInstanceOf('Concat\Di\Tests\Objects\A', $container->a);
        $this->assertInstanceOf('Concat\Di\Tests\Objects\B', $container->b);
    }

    /**
     * @expectedException OutOfBoundsException
     */
    public function testRequestingInvalidName()
    {
        $container = $this->createBasic();
        $container->z;
    }

    protected function _testUnsupportedObject($instances)
    {
        $container = $this->create($instances, [
            'types' => [
                'a' => 'Concat\Di\Tests\Objects\A',
            ],

            'defaults' => [],
        ]);

        $this->assertInstanceOf('Concat\Di\Tests\Objects\A', $container->a);
    }

    /**
     * @expectedException BadMethodCallException
     */
    public function testUnsupportedArrayAccessSet()
    {
        $container = $this->createBasic();
        $container[0] = true;
    }

    /**
     * @expectedException BadMethodCallException
     */
    public function testUnsupportedArrayAccessUnset()
    {
        $container = $this->createBasic();
        unset($container[0]);
    }
}

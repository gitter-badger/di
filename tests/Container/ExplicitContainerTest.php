<?php

namespace Concat\Di\Tests\Container;

use Concat\Di\Tests\Objects\A;
use Concat\Di\Tests\Objects\B;
use Concat\Di\Tests\Objects\C;
use Concat\Di\Tests\Objects\D;

class ExplicitContainerTest extends \PHPUnit_Framework_TestCase
{
    use ContainerTestTrait;

    public $containerClass = 'ExplicitContainer';

    public function testAllInstancesProvided()
    {
        $this->_testAllInstancesProvided([
            'a' => new A(),
            'b' => new B(),
            'c' => new C(),
        ]);
    }

    public function testOnlyOneInstancesProvided()
    {
        $this->_testOnlyOneInstancesProvided([
            'a' => new A(),
        ]);
    }

    public function testSubclassInstance()
    {
        $this->_testSubclassInstance([
            'a' => new A(),
            'b' => new C(),
        ]);
    }

    public function testExpectedCallableTypes()
    {
        $this->_testExpectedCallableTypes([
            'a' => function() { return new A(); },
            'b' => [$this, 'createTestObject'],
        ]);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testUnsupportedObject()
    {
        $this->_testUnsupportedObject([
            'a' => 10,
        ]);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testUnexpectedInstance()
    {
        $this->_testUnexpectedInstance([
            'a' => new A(),
            'b' => new D(),
        ]);
    }
}

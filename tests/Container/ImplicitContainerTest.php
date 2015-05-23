<?php

namespace Concat\Di\Tests\Container;

use Concat\Di\Tests\Objects\A;
use Concat\Di\Tests\Objects\B;
use Concat\Di\Tests\Objects\C;
use Concat\Di\Tests\Objects\D;

class ImplicitContainerTest extends \PHPUnit_Framework_TestCase
{
    use ContainerTestTrait;

    public $containerClass = 'ImplicitContainer';

    public function testAllInstancesProvided()
    {
        $this->_testAllInstancesProvided([new A(), new B(), new C()]);
    }

    public function testOnlyOneInstancesProvided()
    {
        $this->_testOnlyOneInstancesProvided(new A());
    }

    public function testSubclassInstance()
    {
        $this->_testSubclassInstance([new A(), new C()]);
    }

    public function testExpectedCallableTypes()
    {
        $this->_testExpectedCallableTypes([
            function() { return new A(); },
            [$this, 'createTestObject'],
        ]);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testUnsupportedObject()
    {
        $this->_testUnsupportedObject(10);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testUnexpectedInstance()
    {
        $this->_testUnexpectedInstance([new A(), new D()]);
    }
}

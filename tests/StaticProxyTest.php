<?php

namespace XStatic\Test;

use XStatic\StaticProxy;
use XStatic\Test\Fixture\QueueProxy;

/**
 * @covers \XStatic\StaticProxy
 *
 */
class StaticProxyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \BadMethodCallException
     */
    public function testErrorWhenUsingBaseClassDirectly()
    {
        StaticProxy::getInstanceIdentifier();
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testErrorWhenContainerNotSet()
    {
        $rc = new \ReflectionClass('XStatic\StaticProxy');
        $rp = $rc->getProperty('container');
        $rp->setAccessible(true);
        $rp->setValue(null, null);

        QueueProxy::getInstance();
    }

    public function testCanSetAndUseContainer()
    {
        $queue = new \SplQueue;
        $container = new Fixture\Container(array('queue' => $queue));
        QueueProxy::setContainer($container);
        $this->assertSame(
            $container,
            $this->readAttribute('XStatic\Test\Fixture\QueueProxy', 'container')
        );
        $this->assertEquals('queue', QueueProxy::getInstanceIdentifier());
        $this->assertTrue(QueueProxy::isEmpty());
    }
}

<?php

namespace XStatic\Test;

use XStatic\XStatic;

/**
 * @covers \XStatic\XStatic
 */
class XStaticTest extends \PHPUnit_Framework_TestCase
{
    public function testCanCreateStaticProxies()
    {
        // Instantiate XStatic and use setContainer
        $xStatic = new XStatic($this->getMock('Interop\Container\ContainerInterface'));
        $xStatic->setContainer(new Fixture\Container(array('queue' => new \SplQueue)));

        // Register a proxy and enable them
        $xStatic->registerProxy('Queue', 'XStatic\Test\Fixture\StaticQueue');
        $enabled = $xStatic->enable();
        $this->assertTrue($enabled);

        // Enable again, which should be a no-op
        $xStatic->enable();

        // Test the waters and see if the alias was loaded and works as a static interface
        \Queue::enqueue('foo');
        $queue = \Queue::getInstance();
        $this->assertInstanceOf('SplQueue', $queue);
        $this->assertEquals('foo', $queue->dequeue());
    }
}

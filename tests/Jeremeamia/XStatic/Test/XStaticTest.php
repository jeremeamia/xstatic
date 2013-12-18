<?php

namespace Jeremeamia\XStatic\Test;

use Acclimate\Api\Container\ContainerInterface;
use Jeremeamia\XStatic\XStatic;
use Jeremeamia\XStatic\AbstractStaticClass;

class Container extends \ArrayObject implements ContainerInterface
{
    public function get($identifier)
    {
        return $this[$identifier];
    }

    public function has($identifier)
    {
        return isset($this[$identifier]);
    }
}

class StaticQueue extends AbstractStaticClass
{
    public static function getStaticAlias()
    {
        return 'queue';
    }
}

/**
 * @covers \Jeremeamia\XStatic\AbstractStaticClass
 * @covers \Jeremeamia\XStatic\XStatic
 */
class XStaticTest extends \PHPUnit_Framework_TestCase
{
    public function testCanLoadAnAlias()
    {
        // Instantiate XStatic and test setters
        $xStatic = new XStatic($this->getMock('Acclimate\Api\Container\ContainerInterface'));
        $xStatic->setContainer(new Container(array('queue' => new \SplQueue)));
        $xStatic->addAlias('Queue', 'Jeremeamia\XStatic\Test\StaticQueue');

        // Turn it on and try loading an alias
        $xStatic->enableStaticInterfaces();
        $xStatic->loadAlias('Jeremeamia\XStatic\Test\Queue');

        // Test the waters and see if the alias was loaded and works as a static interface
        Queue::enqueue('foo');
        $queue = Queue::getInstance();
        $this->assertInstanceOf('SplQueue', $queue);
        $this->assertEquals('foo', $queue->dequeue());

        // Make sure the loading of the alias was recorded correctly
        $loadedAliases = $xStatic->getLoadedAliases();
        $this->assertArrayHasKey('Queue', $loadedAliases);
        $this->assertContains('Jeremeamia\XStatic\Test', $loadedAliases['Queue']['namespaces']);
    }

    public function testCannotAddSameAliasMoreThanOnce()
    {
        $xStatic = new XStatic($this->getMock('Acclimate\Api\Container\ContainerInterface'));
        $xStatic->addAlias('foo', 'bar');

        $this->setExpectedException('RuntimeException');
        $xStatic->addAlias('foo', 'baz');
    }
}

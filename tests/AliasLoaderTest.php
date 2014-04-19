<?php

namespace XStatic\Test;

use XStatic\AliasLoader;

/**
 * @covers \XStatic\AliasLoader
 */
class AliasLoaderTest extends \PHPUnit_Framework_TestCase
{
    public function testCanAddAliases()
    {
        $loader = new AliasLoader();

        // Starts out empty
        $this->assertEquals([], $this->readAttribute($loader, 'aliases'));

        // Internal array should contain added aliases
        $result = $loader->addAlias('Foo', 'Fake\Foo');
        $this->assertSame($loader, $result);
        $this->assertEquals(
            ['Foo' => 'Fake\Foo'],
            $this->readAttribute($loader, 'aliases')
        );

        // Shouldn't be able to add the same alias again
        $this->setExpectedException('RuntimeException');
        $loader->addAlias('Foo', 'Fake\Bar');
    }

    public function testCanRegisterLoader()
    {
        $loader = new AliasLoader();

        // Loader should not be registered
        $this->assertFalse($loader->isRegistered());

        // Register the loader and check that it worked
        $this->assertTrue($loader->register('Fake\Foo'));
        $this->assertTrue($loader->isRegistered());
        $this->assertTrue($this->verifyAutoloader($loader));

        // Registering the Autoloader is idempotent
        $this->assertTrue($loader->register('Fake\Foo'));
        $this->assertTrue($this->verifyAutoloader($loader));
    }

    public function testCanCreateClassAliasesWithTheLoader()
    {
        $loader = new AliasLoader();
        $loader->addAlias('Foo', __CLASS__);
        $loader->load('Fake\Foo');
        $this->assertTrue(class_exists('Foo'));
    }

    private function verifyAutoloader(AliasLoader $loader)
    {
        $autoloaders = spl_autoload_functions();
        $topLoader = end($autoloaders);
        if (is_array($topLoader)) {
            list($object, $method) = $topLoader;
            return ($object === $loader && $method === 'load');
        } else {
            return false;
        }
    }
}

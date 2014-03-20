<?php

namespace XStatic\Test\Fixture;

use Interop\Container\ContainerInterface;

class Container extends \ArrayObject implements ContainerInterface
{
    public function get($id)
    {
        return $this[$id];
    }

    public function has($id)
    {
        return isset($this[$id]);
    }
}

<?php

namespace Jeremeamia\XStatic;

use Acclimate\Api\Container\ContainerInterface;

/**
 * The implementation of the basic moving parts of a static class interface
 */
abstract class AbstractStaticClass implements StaticClassInterface
{
    /**
     * @var ContainerInterface The container that contains the object instances
     */
    static protected $container;

    public static function setContainer(ContainerInterface $container)
    {
        static::$container = $container;
    }

    public static function getInstance()
    {
        return static::$container->get(static::getStaticAlias());
    }

    public static function __callStatic($method, $args)
    {
        return call_user_func_array(array(static::getInstance(), $method), $args);
    }
}

<?php

namespace Jeremeamia\XStatic;

abstract class AbstractStaticClass implements StaticClassInterface
{
    /**
     * @var ContainerInterface
     */
    static protected $container;

    /**
     * @param ContainerInterface $container
     */
    public static function setContainer(ContainerInterface $container)
    {
        static::$container = $container;
    }

    /**
     * @return ContainerInterface
     */
    public static function getContainer()
    {
        return static::$container;
    }

    /**
     * @return mixed
     */
    public static function getInstance()
    {
        return static::$container->get(static::getStaticAlias());
    }

    public static function __callStatic($method, $args)
    {
        return call_user_func_array(array(static::getInstance(), $method), $args);
    }
}

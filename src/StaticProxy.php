<?php

namespace XStatic;

use Interop\Container\ContainerInterface;

/**
 * The implementation of the basic moving parts of a static class interface
 */
abstract class StaticProxy
{
    /**
     * @var ContainerInterface The container that contains the object instances
     */
    static protected $container;

    /**
     * Sets the container instance that will be used to retrieve the subject
     *
     * @param ContainerInterface $container The container that contains the real subject
     *
     * @return mixed
     */
    public static function setContainer(ContainerInterface $container)
    {
        static::$container = $container;
    }

    /**
     * Retrieves the instance from the container that the static interface is interacting with
     *
     * @return mixed
     * @throws \RuntimeException if the container has not been set
     */
    public static function getInstance()
    {
        if (!(static::$container instanceof ContainerInterface)) {
            throw new \RuntimeException('The subject cannot be retrieved because the container is not set.');
        }

        return static::$container->get(static::getInstanceIdentifier());
    }

    /**
     * Retrieves the identifier that is used to retrieve the subject from the container
     *
     * @return string
     * @throws \BadMethodCallException if the method has not been implemented by a subclass
     */
    public static function getInstanceIdentifier()
    {
        throw new \BadMethodCallException('The' . __METHOD__ . ' method must be implemented by a subclass.');
    }

    public static function __callStatic($method, $args)
    {
        return call_user_func_array(array(static::getInstance(), $method), $args);
    }
}

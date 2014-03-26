<?php

namespace XStatic;

use Interop\Container\ContainerInterface;

/**
 * Implements of the basic Static Proxy logic using `__callStatic()`. This class must be extended in order to provide
 * the correct Instance Identifier
 */
abstract class StaticProxy
{
    /**
     * @var ContainerInterface The Container that provides the object instances
     */
    static protected $container;

    /**
     * Sets the Container instance that will be used to retrieve the subject
     *
     * @param ContainerInterface $container The Container that provides the real subject
     *
     * @return mixed
     */
    public static function setContainer(ContainerInterface $container)
    {
        static::$container = $container;
    }

    /**
     * Retrieves the instance from the Container that the Static Proxy is associated with
     *
     * @return mixed
     * @throws \RuntimeException if the Container has not been set
     */
    public static function getInstance()
    {
        if (!(static::$container instanceof ContainerInterface)) {
            throw new \RuntimeException('The subject cannot be retrieved because the Container is not set.');
        }

        return static::$container->get(static::getInstanceIdentifier());
    }

    /**
     * Retrieves the identifier that is used to retrieve the subject from the Container
     *
     * @return string
     * @throws \BadMethodCallException if the method has not been implemented by a subclass
     */
    public static function getInstanceIdentifier()
    {
        throw new \BadMethodCallException('The' . __METHOD__ . ' method must be implemented by a subclass.');
    }

    /**
     * Performs the proxying of the statically called method to the real subject in the Container
     *
     * @param string $method
     * @param array $args
     *
     * @return mixed
     */
    public static function __callStatic($method, $args)
    {
        return call_user_func_array(array(static::getInstance(), $method), $args);
    }
}

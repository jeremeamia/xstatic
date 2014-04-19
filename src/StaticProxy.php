<?php

namespace XStatic;

use Interop\Container\ContainerInterface;

/**
 * Implements of the basic Static Proxy logic using `__callStatic()`. This class must be extended to create specific
 * Static Proxies in order to provide the correct Instance Identifier for a Proxy Subject
 */
abstract class StaticProxy
{
    /**
     * @var ContainerInterface The Container that provides the Proxy Subjects
     */
    protected static $container;

    /**
     * Sets the Container that will be used to retrieve the Proxy Subject
     *
     * @param ContainerInterface $container The Container that provides the real Proxy Subject
     *
     * @return mixed
     */
    public static function setContainer(ContainerInterface $container)
    {
        static::$container = $container;
    }

    /**
     * Retrieves the instance of the Proxy Subject from the Container that the Static Proxy is associated with
     *
     * @return mixed
     * @throws \RuntimeException if the Container has not been set
     */
    public static function getInstance()
    {
        if (!(static::$container instanceof ContainerInterface)) {
            throw new \RuntimeException('The Proxy Subject cannot be retrieved because the Container is not set.');
        }

        return static::$container->get(static::getInstanceIdentifier());
    }

    /**
     * Retrieves the Instance Identifier that is used to retrieve the Proxy Subject from the Container
     *
     * @return string
     * @throws \BadMethodCallException if the method has not been implemented by a subclass
     */
    public static function getInstanceIdentifier()
    {
        throw new \BadMethodCallException('The' . __METHOD__ . ' method must be implemented by a subclass.');
    }

    /**
     * Performs the proxying of the statically called method to the Proxy Subject in the Container
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

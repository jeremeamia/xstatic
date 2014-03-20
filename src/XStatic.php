<?php

namespace XStatic;

use Interop\Container\ContainerInterface;

/**
 * XStatic allows you to call concrete methods on actual object instances from a container (service locator) by using
 * static method invocation on a proxy class. An alias is registered for the proxy class to make it easy to access.
 * Using static proxies gives the appearance that you are calling static methods, which is generally considered a code
 * smell; however, the methods are actually being delegated to an object instance using the __callStatic() magic method.
 * The technique is the same that is used by the Laravel framework with their unfortunately-named "facades" feature.
 */
class XStatic
{
    /**
     * @var ContainerInterface The container to inject into the static classes and that holds the actual instances
     */
    private $container;

    /**
     * @var AliasLoaderInterface ???
     */
    private $aliasLoader;

    /**
     * @param ContainerInterface   $container   A container that holds the actual instances
     * @param AliasLoaderInterface $aliasLoader ???
     */
    public function __construct(ContainerInterface $container, AliasLoaderInterface $aliasLoader = null)
    {
        $this->container = $container;
        $this->aliasLoader = $aliasLoader ?: new AliasLoader();
    }

    /**
     * Enables the static class proxies by injecting the container object and registering the XStatic autoloader
     *
     * @param string|null $rootNamespace
     *
     * @return bool
     */
    public function enable($rootNamespace = null)
    {
        // If XStatic is already enabled, this is a no-op
        if ($this->aliasLoader->isRegistered()) {
            return true;
        }

        // Register the loader to handle aliases and link the proxies to the container
        if ($this->aliasLoader->register($rootNamespace)) {
            StaticProxy::setContainer($this->container);
        }

        return $this->aliasLoader->isRegistered();
    }

    /**
     * ???
     *
     * @param string $alias
     * @param string $proxyFqcn
     *
     * @return $this
     */
    public function registerProxy($alias, $proxyFqcn)
    {
        $this->aliasLoader->addAlias($alias, $proxyFqcn);

        return $this;
    }

    /**
     * Sets the container object that provides the actual subject instances
     *
     * @param ContainerInterface $container Container that provides the subjects
     *
     * @return $this
     */
    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;
        StaticProxy::setContainer($this->container);

        return $this;
    }
}

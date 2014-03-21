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
     * @var ContainerInterface Container to inject into the static classes and that holds the actual instances
     */
    private $container;

    /**
     * @var AliasLoaderInterface Loader object that loads the static proxies when aliases are referenced
     */
    private $aliasLoader;

    /**
     * @param ContainerInterface   $container   Container that holds the actual instances
     * @param AliasLoaderInterface $aliasLoader Loader object that loads the static proxies when aliases are referenced
     */
    public function __construct(ContainerInterface $container, AliasLoaderInterface $aliasLoader = null)
    {
        $this->container = $container;
        $this->aliasLoader = $aliasLoader ?: new AliasLoader();
    }

    /**
     * Enables the static class proxies by injecting the container object and registering the XStatic autoloader
     *
     * @param bool|string $rootNamespace The namespace that the alias should be created in.
     *
     * @return bool
     * @see \XStatic\AliasLoaderInterface::register()
     */
    public function enableProxies($rootNamespace = false)
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
     * Registers a static proxy class with XStatic and links it with an alias
     *
     * @param string $alias     Alias to associate with the static proxy class
     * @param string $proxyFqcn FQCN of the static proxy class
     *
     * @return $this
     */
    public function registerProxy($alias, $proxyFqcn)
    {
        $this->aliasLoader->addAlias($alias, $proxyFqcn);

        return $this;
    }

    /**
     * Sets the container object that provides the actual subjects' instances
     *
     * @param ContainerInterface $container Instance of a Container (or Service Locator)
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

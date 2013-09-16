<?php

namespace Jeremeamia\XStatic;

/**
 * XStatic allows you to register aliases to static classes so you can create static interfaces, or "facades", to an
 * object instance in a container
 */
class XStatic
{
    /**
     * @var string The current version of the XStatic library
     */
    const VERSION = '0.1.0';

    /**
     * @var array Information about aliases that have been loaded
     */
    private static $loadedAliases = array();

    /**
     * @var array Registered aliases to static class interfaces
     */
    private $aliases = array();

    /**
     * @var ContainerInterface The container to inject into the static classes and that holds the actual instances
     */
    private $container;

    /**
     * @param ContainerInterface $container A container that holds the actual instances
     * @param array              $aliases   An associative array of aliases to static class FQCNs
     */
    public function __construct(ContainerInterface $container, array $aliases = array())
    {
        $this->setContainer($container);
        $this->setAliases($aliases);
    }

    /**
     * Enables static class interfaces by injecting the container object and registering the XStatic autoloader
     *
     * @return $this
     */
    public function enableStaticInterfaces()
    {
        AbstractStaticClass::setContainer($this->container);
        spl_autoload_register(array($this, 'loadAlias'), true, true);

        return $this;
    }

    /**
     * Sets the container object that holds the actual instances
     *
     * @param ContainerInterface $container A container that holds the actual instances
     *
     * @return $this
     */
    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;

        return $this;
    }

    /**
     * Creates an alias to a static class interface
     *
     * @param string $alias An alias to associate with a static class
     * @param string $fqcn  An FQCN to a static class
     *
     * @return $this
     */
    public function addAlias($alias, $fqcn)
    {
        $this->aliases[$alias] = $fqcn;

        return $this;
    }

    /**
     * Removes an alias and disassociates it from a static class interface
     *
     * @param string $alias The alias to disassociate from a static class
     *
     * @return $this
     */
    public function removeAlias($alias)
    {
        unset($this->aliases[$alias]);

        return $this;
    }

    /**
     * Sets the entire associative array of aliases
     *
     * @param array $aliases An associative array of aliases to static class FQCNs
     *
     * @return $this
     */
    public function setAliases(array $aliases)
    {
        $this->aliases = $aliases;

        return $this;
    }

    /**
     * Returns information about aliases that have been loaded included what namespace they were loaded from
     *
     * @return array
     */
    public function getLoadedAliases()
    {
        return self::$loadedAliases;
    }

    /**
     * Loads an alias by creating a real class alias to the static class. This is used as an autoloader function
     *
     * @param string $alias An alias to load
     *
     * @return bool
     */
    public function loadAlias($alias)
    {
        $namespace = '';
        if (false !== ($pos = strrpos($alias, '\\'))) {
            $namespace = substr($alias, 0, $pos + 1);
            $alias = substr($alias, $pos + 1);
        }

        if (isset($this->aliases[$alias])) {
            class_alias($this->aliases[$alias], $namespace . $alias);
            $this->recordLoadedAlias($alias, $namespace);
        }
    }

    /**
     * Records the loading of an alias
     *
     * @param string $alias     The alias that was loaded
     * @param string $namespace The namespace it was loaded from
     */
    private function recordLoadedAlias($alias, $namespace)
    {
        if (!isset(self::$loadedAliases[$alias])) {
            self::$loadedAliases[$alias] = array(
                'class'      => $this->aliases[$alias],
                'namespaces' => array(),
            );
        }

        self::$loadedAliases[$alias]['namespaces'][] = trim($namespace, '\\');
    }
}

<?php

namespace XStatic;

class AliasLoader implements AliasLoaderInterface
{
    /**
     * @var array Registered aliases to static class interfaces
     */
    private $aliases;

    /**
     * @var bool ???
     */
    private $isRegistered = false;

    /**
     * @var array Information about aliases that have been loaded
     */
    private $loadedAliases = array();

    /**
     * @var string ???
     */
    private $rootNamespace = self::ROOT_GLOBAL;

    /**
     * @param array $aliases
     */
    public function __construct(array $aliases = array())
    {
        $this->aliases = $aliases;
    }

    /**
     * Creates an alias to a static class interface
     *
     * @param string $alias An alias to associate with a static class
     * @param string $fqcn  An FQCN to a static class
     *
     * @return $this
     * @throws \RuntimeException if you try to add an alias that has already been added
     */
    public function addAlias($alias, $fqcn)
    {
        if (isset($this->aliases[$alias])) {
            throw new \RuntimeException("The alias, {$alias}, has already been added and cannot be changed.");
        } else {
            $this->aliases[$alias] = $fqcn;
        }

        return $this;
    }

    public function getAliases()
    {
        return $this->aliases;
    }

    /**
     * Returns information about aliases that have been loaded included what namespace they were loaded from
     *
     * @return array
     */
    public function getLoadedAliases()
    {
        return $this->loadedAliases;
    }

    public function isRegistered()
    {
        return $this->isRegistered;
    }

    /**
     * Loads an alias by creating a real class alias to the requested class. This is used as an autoload function
     *
     * @param string $fqcn ???
     *
     * @return bool
     */
    public function load($fqcn)
    {
        // Determine the alias and namespace from the requested class
        $alias = $fqcn;
        $namespace = '';
        if (false !== ($pos = strrpos($fqcn, '\\'))) {
            $namespace = substr($alias, 0, $pos + 1);
            $alias = substr($alias, $pos + 1);
        }

        // If the alias has been registered, handle it
        if (isset($this->aliases[$alias])) {
            // Determine what namespace the alias should be loaded into, depending on the root namespace
            $namespace = ($this->rootNamespace === self::ROOT_ANY) ? $namespace : $this->rootNamespace;

            // Create the class alias
            class_alias($this->aliases[$alias], $namespace . $alias);

            // Keep track of the namespace this alias was loaded into
            if (!isset($this->loadedAliases[$alias])) {
                $this->loadedAliases[$alias] = array();
            }
            $this->loadedAliases[$alias][] = $namespace ?: '\\';
        }
    }

    public function register($rootNamespace = null)
    {
        if ($this->isRegistered) {
            return true;
        }

        if ($rootNamespace) {
            $this->rootNamespace = $rootNamespace;
        }

        return $this->isRegistered = spl_autoload_register(array($this, 'load'));
    }
}

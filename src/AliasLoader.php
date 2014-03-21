<?php

namespace XStatic;

class AliasLoader implements AliasLoaderInterface
{
    /**
     * @var array Aliases that can be loaded
     */
    private $aliases;

    /**
     * @var bool Whether or not the loader has been registered
     */
    private $isRegistered = false;

    /**
     * @var string The namespace that the alias should be created in
     */
    private $rootNamespace = false;

    /**
     * @param array $aliases
     */
    public function __construct(array $aliases = array())
    {
        $this->aliases = $aliases;
    }

    public function addAlias($alias, $fqcn)
    {
        if (isset($this->aliases[$alias])) {
            throw new \RuntimeException("The alias, {$alias}, has already been added and cannot be changed.");
        } else {
            $this->aliases[$alias] = $fqcn;
        }

        return $this;
    }

    public function isRegistered()
    {
        return $this->isRegistered;
    }

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
            $namespace = ($this->rootNamespace === true) ? $namespace : $this->rootNamespace;

            // Create the class alias
            class_alias($this->aliases[$alias], $namespace . $alias);
        }
    }

    public function register($rootNamespace = false)
    {
        if ($this->isRegistered) {
            return true;
        }

        if ($rootNamespace) {
            $this->rootNamespace = is_string($rootNamespace) ? rtrim($rootNamespace, '\\') . '\\' : $rootNamespace;
        }

        return $this->isRegistered = spl_autoload_register(array($this, 'load'));
    }
}

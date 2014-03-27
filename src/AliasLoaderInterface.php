<?php

namespace XStatic;

/**
 * An Alias Loader is registered as an autoloader, and creates class aliases based on the aliases added to the loader
 */
interface AliasLoaderInterface
{
    /**
     * Creates an alias to a fully-qualified class name (FQCN)
     *
     * @param string $alias Alias to associate with the class
     * @param string $fqcn  FQCN of the class
     *
     * @return $this
     * @throws \RuntimeException if the alias has already been added
     */
    public function addAlias($alias, $fqcn);

    /**
     * Checks if the the Alias Loader has already been registered
     *
     * @return bool
     */
    public function isRegistered();

    /**
     * Loads an alias by creating a class_alias() to the requested class. This is used as an autoload function
     *
     * @param string $fqcn FQCN of the class to be loaded
     */
    public function load($fqcn);

    /**
     * Registers the Alias Loader as an autoloader so that aliases can be resolved via `class_alias()`
     *
     * The Root Namespace can be configured such that the alias is created in a particular namespace. Valid values for
     * the `$rootNamespace` parameter are as follows:
     *
     * - `false` - The alias will be created in the global namespace (default)
     * - `true` - The alias will be created in the namespace where it is referenced
     * - Any specific namespace (e.g., 'Foo\\Bar') - The alias is created in the specified namespace
     *
     * @param bool|string $rootNamespace Namespace where the alias should be created
     *
     * @return bool Returns true if the registration was successful
     */
    public function register($rootNamespace = false);
}

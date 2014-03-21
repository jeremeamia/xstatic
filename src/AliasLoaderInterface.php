<?php

namespace XStatic;

/**
 * An alias loader is registered as an autoloader, and creates class aliases based on the aliases added to the loader
 */
interface AliasLoaderInterface
{
    const ROOT_GLOBAL = '';
    const ROOT_ANY = '*';

    /**
     * Creates an alias to a static class
     *
     * @param string $alias Alias to associate with the class
     * @param string $fqcn  FQCN of the class
     *
     * @return $this
     * @throws \RuntimeException if the alias has already been added
     */
    public function addAlias($alias, $fqcn);

    /**
     * Checks if the the loader has already been registered
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
     * @param null $rootNamespace The namespace that the alias should be created in. There are 3 types of valid values:
     *                            * self::ROOT_GLOBAL (i.e., '')- Alias created in the global namespace
     *                            * self::ROOT_ANY (i.e., '*') - Alias created in the namespace where it is referenced
     *                            * Custom namespace (e.g., 'Foo\\Bar\\') - Alias created in the provided namespace
     *
     * @return bool Returns true if the registration was successful
     */
    public function register($rootNamespace = null);
}

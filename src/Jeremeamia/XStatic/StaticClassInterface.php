<?php

namespace Jeremeamia\XStatic;

/**
 * The interface of a static class interface that is meant to interact with an object instance in a service container
 */
interface StaticClassInterface
{
    /**
     * Retrieves the instance from the container that the static interface is interacting with
     *
     * @return mixed
     */
    public static function getInstance();

    /**
     * Retrieves the alias that is used to reference the instance in the container
     *
     * @return string
     */
    public static function getStaticAlias();

    /**
     * Sets the service container that is to be used to retrieve the instance
     *
     * @param ContainerInterface $container The container that contains the object instances
     *
     * @return mixed
     */
    public static function setContainer(ContainerInterface $container);
}

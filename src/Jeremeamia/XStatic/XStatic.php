<?php

namespace Jeremeamia\XStatic;

class XStatic
{
    const VERSION = '0.1.0';

    /**
     * @var array
     */
    private $aliases = array();

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ContainerInterface $container
     * @param array              $aliases
     */
    public function __construct(ContainerInterface $container, array $aliases = array())
    {
        $this->setContainer($container);
        $this->setAliases($aliases);
    }

    /**
     * @return $this
     */
    public function enableStaticInterfaces()
    {
        AbstractStaticClass::setContainer($this->container);
        spl_autoload_register(array($this, 'loadAlias'), true, true);

        return $this;
    }

    /**
     * @param ContainerInterface $container
     *
     * @return $this
     */
    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;

        return $this;
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @param string $alias
     * @param string $fqcn
     *
     * @return $this
     */
    public function addAlias($alias, $fqcn)
    {
        $this->aliases[$alias] = $fqcn;

        return $this;
    }

    /**
     * @param string $alias
     *
     * @return $this
     */
    public function removeAlias($alias)
    {
        unset($this->aliases[$alias]);

        return $this;
    }

    /**
     * @param array $aliases
     *
     * @return $this
     */
    public function setAliases(array $aliases)
    {
        $this->aliases = $aliases;

        return $this;
    }

    /**
     * @return array
     */
    public function getAliases()
    {
        return $this->aliases;
    }

    /**
     * @param string $alias
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
            return class_alias($this->aliases[$alias], $namespace . $alias);
        }
    }
}

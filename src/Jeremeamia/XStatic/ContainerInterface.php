<?php

namespace Jeremeamia\XStatic;

interface ContainerInterface
{
    /**
     * @param string $name
     *
     * @return mixed
     */
    public function get($name);

    /**
     * @param $name
     *
     * @return bool
     */
    public function has($name);
}

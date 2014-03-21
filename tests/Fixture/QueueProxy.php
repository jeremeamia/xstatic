<?php

namespace XStatic\Test\Fixture;

use XStatic\StaticProxy;

/**
 * @method static \SplQueue getInstance()
 */
class QueueProxy extends StaticProxy
{
    public static function getInstanceIdentifier()
    {
        return 'queue';
    }
}

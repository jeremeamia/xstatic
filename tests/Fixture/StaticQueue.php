<?php

namespace XStatic\Test\Fixture;

use XStatic\StaticProxy;

class StaticQueue extends StaticProxy
{
    public static function getInstanceIdentifier()
    {
        return 'queue';
    }
}

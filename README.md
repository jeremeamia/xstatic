# XStatic

*Static interfaces without the static pitfalls* • by [Jeremy Lindblom](https://twitter.com/jeremeamia)

## Introduction

**TL;DR: XStatic is a PHP library for enabling *static proxy interfaces*—similar to Laravel 4 "facades"—but with any
PHP project.**

### Why?

> Isn't using static methods considered a bad practice?

Using static methods and classes makes your code harder to test. This is because your code becomes tightly coupled to
the class being referenced statically, and mocking static methods for unit tests is difficult. For this and other
reasons, using static methods is generally discouraged by object-oriented programming (OOP) experts. Generally,
techniques involving design patterns like *Service Locator* and *Dependency Injection* (DI) are preferred for managing
object dependencies and composition.

> But... using static methods is really easy.

True, and PHP developers that prefer frameworks like CodeIgniter, Laravel, Kohana, and FuelPHP are very accustomed to
using static methods in their application development. In some cases, it is an encouraged practice among these
communities, who argue that it makes the code more readable and contributes to *Rapid Application Development* (RAD).

> So, is there any kind of compromise?

Yep! Laravel 4 has a concept called "facades" (Note: This is not the same as the [Facade design
pattern](http://en.wikipedia.org/wiki/Facade_pattern)). These act as a static interface, or proxy, to an actual object
instance stored in a service container. The static proxy is linked to the container using a few tricks, including
defining class aliases via PHP's `class_alias()` function, and the use of the magic `__callStatic()` method. We can
thank [Taylor Otwell](https://twitter.com/taylorotwell) for developing this technique.

> OK, then what is the point of XStatic?

XStatic uses the same technique as Laravel's "facades" system, but provides two additional, but important, features:

1. **It works with any framework's service container** - XStatic relies on the `ContainerInterface` of the
   [container-interop](https://github.com/container-interop/container-interop) project. You can use the [Acclimate
   library](https://github.com/jeremeamia/acclimate-container) to adapt any third-party containers to the normalized
   container interface that XStatic depends on.
2. **It works within any namespace** - XStatic injects an autoloader onto the stack, so no matter what namespace or
   scope you try to reference your aliased static proxy from, it will pass through the XStatic autoloader. You can
   configure XStatic to create the aliases in the global namespace, the current namespace, or a specific namespace.

> Oh, and why is it called XStatic?

Two reasons:

1. It **removes the static-ness** of making static method invocations, since the method calls are proxied to actual
   object instances.
2. It is pronounced like the word "ecstatic", because it is meant to provide developers (some of them at least) with
   a sense of joy.

## Usage

To show you how to use XStatic, I will show you a simple [Silex](http://silex.sensiolabs.org/) application.

Your application bootstrap:

```php
<?php

// Include the Composer autoloader, of course
require 'vendor/autoload.php';

use Acclimate\Container\ContainerAcclimator;
use XStatic\XStatic;
use Silex\Application;
use Silex\Provider\TwigServiceProvider;

// Setup your Silex app/container
$app = new Application;
$app->register(new TwigServiceProvider, array(
    'twig.path' => __DIR__ . '/templates',
));
$app['db'] = function () {
    return new PDO('mysql:dbname=testdb;host=127.0.0.1', 'dbuser', 'dbpass');
};
$app->get('/', 'MyApp\Controller\Home::index'); // Routes "/" to a controller object

// Setup and enable XStatic
$acclimator = new ContainerAcclimator();
$proxyManager = new ProxyManager($acclimator->acclimate($app));
$proxyManager->addProxy('View', 'MyApp\Proxy\Twig');
$proxyManager->addProxy('DB', 'MyApp\Proxy\Pdo');
$proxyManager->enable(ProxyManager::ROOT_NAMESPACE_ANY);

// Run the app
$app->run();
```

Your Static Proxy classes:

```php
namespace MyApp\Proxy
{
    use XStatic\StaticProxy;

    class Pdo extends StaticProxy
    {
        public function getInstanceIdentifier()
        {
            return 'db';
        }
    }

    class Twig extends StaticProxy
    {
        public function getInstanceIdentifier()
        {
            return 'twig';
        }
    }
}
```

Your controller class:

```php
namespace MyApp\Controller;

class Home
{
    public function index()
    {
        // It just works!
        View::render('home.index', array(
            'articles' => DB::query('SELECT * FROM articles')
        );
    }
}
```

Pretty cool, huh? Some interesting things to note about this example is that we've actually hidden the fact that we are
using PDO and Twig from the controller. We could easily swap something else in that uses the same interfaces, and the
controller code would not need to be altered. All we would need to do is put different objects into the application
container. In fact, that is *exactly* how testing the controller would work. The test could be bootstrapped with mock or
stub objects put into the container.

*Static interfaces without the static pitfalls.*

## Inspiration

This library is heavily inspired by the [Facades](http://laravel.com/docs/facades) feature in the
[Laravel 4 Framework](http://laravel.com/).

## Disclaimer

I would not consider myself to be *for* or *against* the use of static proxy interfaces (or Laravel's "facades"), but I
do think it is a fascinating and unique idea, and that it is very cool that you can write code this way and still have
it work and be testable. I am curious to see if developers, especially library and framework developers, find ways to
use, *but not require*, these static proxy interfaces in order to make their projects appeal to a wider range of PHP
developers.

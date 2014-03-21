# XStatic

*Static interfaces without the static pitfalls* • by [Jeremy Lindblom](https://twitter.com/jeremeamia)

## Intro

**TL;DR: XStatic is a library for enabling static proxy interfaces, like Laravel 4 "facades", but in any PHP project.**

Using static methods and classes makes your code harder to test. This is because your code becomes tightly coupled to
the class being referenced statically, and mocking static methods for unit tests is difficult. For this and other
reasons, using static methods is generally discouraged by object-oriented programming (OOP) purists. Generally,
techniques involving design patterns like *Service Locator* and *Dependency Injection* (DI) are preferred for managing
object dependencies and composition.

However, PHP developers that prefer frameworks like CodeIgniter, Laravel, Kohana, and FuelPHP are very accustomed to
using static methods in their application development. In some cases, it is a generally encouraged practice among these
developers, who argue that it makes the code more readable and contributes to *Rapid Application Development* (RAD).

Fortunately, in Laravel 4, Taylor Otwell developed a compromise. Laravel 4 has a concept called "facades" (Note: Not the
same as the [Facade design pattern](http://en.wikipedia.org/wiki/Facade_pattern)). These act as a static interface, or
proxy, to an actual object instance stored in a service container. The static proxy is linked to the container using
a few tricks, including defining class aliases via PHP's `class_alias()` function.

**XStatic** is a library for enabling these static proxy interfaces in a similar way to the approach taken by Laravel 4
"facades". It's called "XStatic", because it removes the static-ness static method invocations. It is also pronounced
like the word "ecstatic", because I hope that it makes developers happy.

Sounds pretty good so far, right? Well, there are two additional features that really make XStatic cool:

1. **It works with any framework's service container** - XStatic relies on the `ContainerInterface` of the
   [container-interop](https://github.com/container-interop/container-interop) project. You can use the [Acclimate
   library](https://github.com/jeremeamia/acclimate-container) to adapt third-party containers to the normalized
   container interface that XStatic depends on.
2. **It works within any namespace** - XStatic injects an autoloader onto the stack, so no matter what namespace or
   scope you try to reference your aliased static proxy from, it will pass through the XStatic autoloader. You can
   configure XStatic to create the aliases in the global namespace, the current namespace, or a specific namespace.

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

// Setup your Silex app
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
$xs = new XStatic($acclimator->acclimate($app));
$xs->registerProxy('View', 'MyApp\Proxy\Twig');
$xs->registerProxy('DB', 'MyApp\Proxy\Pdo');
$xs->enableProxies(XStatic::ROOT_NAMESPACE_ANY);

$app->run();
```

Your static class interfaces:

```php
namespace MyApp\Proxy
{
    use \XStatic\StaticProxy;

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

Your controller:

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
container. In fact, this is *exactly* how testing the controller would work. The test would be bootstrapped with mock or
stub objects put into the container.

*Static interfaces without the static pitfalls.*

## Inspiration

This library is heavily inspired by the [Facades](http://laravel.com/docs/facades) feature in the
[Laravel 4 Framework](http://laravel.com/).

## FAQs

1. "Why do you need to declare those classes that only have the `getInstanceIdentifier()` method?" — This class is what
allows XStatic to determine what is being called, and what it is associated with. It's not possible to create a solution
that does not require these classes to be defined, because there is **no** way in PHP to determine the name of the alias
called, not even by examining a backtrace.

## Disclaimer

I would not consider myself to be *for* or *against* the use of static proxy interfaces (or Laravel's "facades"), but I
do think it is a fascinating and unique idea, and that it is very cool that you can write code this way and still have
it work and be testable. I am curious to see if developers, especially library and framework developers, find ways to
use, *but not require*, these static proxy interface in order to make their projects appeal to a wider range of PHP
developers.

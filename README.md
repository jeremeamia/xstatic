# XStatic

*Static interfaces without the static pitfalls.*

by [Jeremy Lindblom](https://twitter.com/jeremeamia)

Version 0.2.0

## Intro

**TL;DR: XStatic is a library for enabling static proxy interfaces, like the Laravel 4 "facades", but in any PHP project.**

Using static methods and classes makes your code harder to test. This is because your code becomes tightly coupled to
the class being referenced statically, and mocking static methods for unit tests is difficult. For this and other
reasons, using static methods is generally discouraged by object-oriented programming (OOP) purists. Generally,
techniques involving design patterns like *Service Locator* and *Dependency Injection* (DI) are preferred for managing
object dependencies and composition.

However, PHP developers that prefer frameworks like CodeIgniter, Laravel, Kohana, and FuelPHP are very accustomed to
using static methods in their application development. In some cases, it is a generally encouraged practice among these developers, who argue that it makes the code more readable and contributes to *Rapid Application Development* (RAD).

Fortunately, in Laravel 4, Taylor Otwell developed a compromise. Laravel 4 has a concept called *Facades*, which act
as a static interface, or proxy, to an actual object instance stored in a service container. The static interface is linked to the container by defining class aliases using PHP's `class_alias()` function.

**XStatic** is a library for enabling these static proxy interfaces in a similar way to the approach taken by
Laravel 4 "facades". It's called "XStatic", because it removes the static-ness static classes. It is also pronounced like the word "ecstatic", because I hope that it makes developers happy.

Sounds pretty good so far, right? Well, there are two additional features that really make XStatic cool:

1. **It works with any framework's service container** - XStatic relies on the `ContainerInterface` of the
   [Acclimate](https://github.com/jeremeamia/acclimate-container) library. Acclimate can be used to adapt third-party 
   containers to its normalized container interface, which is what XStatic depends on.
2. **It works within any namespace** - XStatic injects an autoloader onto the stack, so no matter what namespace or
   scope you try to reference your aliased static interface from, it will pass through the XStatic autoloader and create
   the corresponding `class_alias` needed to make it work.

## Usage

To show you how to use XStatic, I will show you a simple [Silex](http://silex.sensiolabs.org/) application.

Your application bootstrap:

```php
<?php

// Include the Composer autoloader
require 'vendor/autoload.php';

use Acclimate\Container\ContainerAcclimator;
use Jeremeamia\XStatic\XStatic;
use Silex\Application;
use Silex\Provider\TwigServiceProvider;

// Setup your app
$app = new Application;
$app->register(new TwigServiceProvider, array(
    'twig.path' => __DIR__ . '/templates',
));
$app['db'] = function () {
    return new PDO('mysql:dbname=testdb;host=127.0.0.1', 'dbuser', 'dbpass');
};
$app->get('/', 'MyApp\Controller\Home::index'); // Routes "/" to a controller object

// Setup XStatic
$acclimator = new ContainerAcclimator();
$xstatic = new XStatic($acclimator->acclimate($app));
$xstatic->addAlias('View', 'MyApp\Service\StaticTwig');
$xstatic->addAlias('DB', 'MyApp\Service\StaticPdo');
$xstatic->enableStaticInterfaces();

$app->run();
```

Your static class interfaces:

```php
namespace MyApp\Service
{
    use Jeremeamia\XStatic\AbstractStaticClass;

    class StaticPdo extends AbstractStaticClass
    {
        public function getStaticAlias()
        {
            return 'db';
        }
    }

    class StaticTwig extends AbstractStaticClass
    {
        public function getStaticAlias()
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
stub objects put into the application container.

*Static interfaces without the static pitfalls.*

## Inspiration

This library is heavily inspired by the [Facades](http://laravel.com/docs/facades) feature in the
[Laravel 4 Framework](http://laravel.com/).

## Disclaimer

I would not consider myself to be *for* or *against* the use of static proxy interfaces (or "facades" in Laravel), but I do think it is cool that you can write code this way and have it work and still be testable. I foresee that developers,
especially library and framework developers, may find ways to use, but not require, these static interfaces in order to
make their projects appeal to a wider range of PHP developers.

Feedback is welcome. :-)

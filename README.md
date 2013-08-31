# XStatic

By Jeremy Lindblom

Static interfaces without static pitfalls.

## Intro

Using static methods and classes makes your code harder to test. This is because your code becomes tightly coupled to
the class being referenced statically, and mocking static methods for unit tests is difficult. For this and other
reasons, using static methods is generally discouraged by object-oriented programming (OOP) purists. Generally, techniques involving design patterns like *Service Locator* and *Dependency Injection* (DI) are preferred.

However, PHP developers that prefer frameworks like CodeIgniter, Laravel, Kohana, or FuelPHP are very accustomed to
using static methods. In fact, it is an encouraged practice among these developers, who argue that it makes the code
more readable and contributes to *Rapid Application Development* (RAD).

Fortunately, in Laravel 4, Taylor Otwell developed a compromise. Laravel 4 has a concept called *Facades*, which act
as a static interface to an actual object instance stored in a dependency injection container. The static interface is
linked to the container by defining class aliases using PHP's `class_alias` function.

**XStatic** is a library for enabling static interfaces in a similar way to the approach taken by Laravel 4. It's called
"XStatic", because it takes the static-ness out of static classes. It is also pronounced like the word "ecstatic", so I
hope it makes you happy.

## Usage

```php
<?php

// Setup autoloading
namespace {
    require __DIR__ . '/../vendor/autoload.php';
}

// Define a service layer for the application
namespace My\Lib\Service {
    use Jeremeamia\XStatic\AbstractStaticClass;

    class Adder {
        public function add($a, $b) {
            return $a + $b;
        }
    }

    class StaticAdder extends AbstractStaticClass {
        public static function getStaticAlias() {
            return 'adder';
        }
    }
}

// Define a very basic DI container
namespace My\Lib\Di {
    use Jeremeamia\XStatic\ContainerInterface;

    class Container implements ContainerInterface {
        private $things = array();
        public function __construct(array $things) {
            $this->things = $things;
        }
        public function get($name) {
            if ($this->has($name)) return $this->things[$name]; else die('FAIL!');
        }
        public function has($name) {
            return isset($this->things[$name]);
        }
    }
}

// Define the application
namespace My\App {
    class App {
        public function run() {
            // Use the static interface to Adder
            echo Adder::add(4, 6);
            // 10
        }
    }
}

// Demonstrate an app bootstraping process with XStatic
namespace {
    use Jeremeamia\XStatic\XStatic;
    use My\Lib\Di\Container;
    use My\Lib\Service\Adder;
    use My\App\App;

    // Setup container
    $container = new Container(array(
        'adder' => new Adder()
    ));

    // Setup XStatic
    $x = new XStatic($container);
    $x->addAlias('Adder', 'My\Lib\Service\StaticAdder');
    $x->enableStaticInterfaces();

    // Run App
    $app = new App;
    $app->run();
}
```

# Dependency Injection [![PHP Composer](https://github.com/EdmondDantes/di/actions/workflows/php.yml/badge.svg)](https://github.com/EdmondDantes/di/actions/workflows/php.yml)

`Dependency Injection` (DI) is a lightweight `PHP` library for dependency injection
for `stateful` application.

The library is designed for `PHP 8.4` using the `LazyProxy API`.

### Features

* Zero configuration. 
Ability to inject dependencies without modifying the code in the dependent class.
* Constructor injection
* Support for the concept of `environment`/`scope` for dependency lookup
* Injection of dependencies into properties
* Injecting configuration values as a Dependency
* Lazy loading
* Handling circular dependencies
* Support php-attributes for describing dependencies

### Installation

You can install Dependency Injector using Composer. Run the following command:

```bash
composer require ifcastle/di
```

### Architecture

![Architecture](docs/images/components.svg)

### Usage

The example below demonstrates how the library works with the `SomeClass` class, 
which implements the SomeInterface interface.

The class definition does not depend on the `Dependency Injection` implementation. 
Dependencies are injected via the class `constructor`.

The library automatically binds dependencies to their interfaces.

```php
declare(strict_types=1);

use IfCastle\DI\ContainerBuilder;
use IfCastle\DI\Lazy;

readonly class SomeClass implements SomeInterface
{
    public function __construct(
        private SomeRequiredInterface $required,
        private SomeOptionalInterface $optional = null
        private int $configValue = 0
    ) {}

    public function test(): void
    {
        $this->some->someMethod();
    }
}

// 1. Create a container builder

$builder                    = new ContainerBuilder();
// 2. Define the constructible dependencies
$builder->bindConstructible(SomeInterface::class, SomeClass::class);
$builder->bindConstructible(SomeRequiredInterface::class, SomeRequiredClass::class);
$builder->bindConstructible(SomeOptionalInterface::class, SomeOptionalClass::class);
// 2. Define the configuration values
$builder->set('configValue', 42);

// 3. Build the container
$container                  = $builder->buildContainer(new Resolver());

// 4. Get the dependency
$some                       = $container->resolveDependency(SomeInterface::class);

```


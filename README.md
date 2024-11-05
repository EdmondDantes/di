# Dependency Injection [![PHP Composer](https://github.com/EdmondDantes/di/actions/workflows/php.yml/badge.svg)](https://github.com/EdmondDantes/di/actions/workflows/php.yml)

`Dependency Injection` (DI) is a lightweight `PHP` library for dependency injection
for `stateful` application.

### Features

* Zero configuration. 
Ability to inject dependencies without modifying the code in the dependent class.
* Constructor injection
* Support for the concept of `environment`/`scope` for dependency lookup
* Injection of dependencies into properties
* Injecting configuration values as a Dependency
* Lazy loading
* Support php-attributes for describing dependencies

### Installation

You can install Dependency Injector using Composer. Run the following command:

```bash
composer require ifcastle/di
```

### Architecture

![Architecture](docs/images/components.svg)
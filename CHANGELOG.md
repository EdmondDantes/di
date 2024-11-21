# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [0.8.0] - 2024-11-22

### Added
- Zero configuration.
  Ability to inject dependencies without modifying the code in the dependent class.
- Constructor injection
- Support for the concept of `environment`/`scope` for dependency lookup
- Injection of dependencies into properties
- Injecting configuration values as a Dependency
- Lazy loading
- Handling circular dependencies
- Support php-attributes for describing dependencies
<?php
declare(strict_types=1);

namespace IfCastle\DI;

interface BuilderInterface
{
    public function isBound(string ...$keys): bool;
    
    public function bind(string|array $interface, DependencyInterface|InitializerInterface $dependency, bool $isThrow = true, bool $redefine = false): static;
    public function bindConstructible(string|array $interface, string $class, bool $isThrow = true, bool $redefine = false): static;
    public function bindInjectable(string|array $interface, string $class, bool $isThrow = true, bool $redefine = false): static;
    public function bindObject(string|array $interface, object $object, bool $isThrow = true, bool $redefine = false): static;
    public function bindSelfReference(string|array $interface = null, bool $isThrow = true, bool $redefine = false): static;
    public function bindInitializer(string|array $interface, callable $initializer, bool $isThrow = true, bool $redefine = false): static;
    
    public function get(string $key): mixed;
    public function set(string $key, mixed $value): static;
    
    public function getKeyAsString(string $key): string;
    
    public function buildContainer(
        ResolverInterface  $resolver,
        ContainerInterface $parentContainer = null,
        bool               $isWeakParent = false
    ): ContainerInterface;
}
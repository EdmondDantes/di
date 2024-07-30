<?php
declare(strict_types=1);

namespace IfCastle\DI;

interface BuilderInterface
{
    public function isBound(string ...$keys): bool;
    
    public function bind(string|array $interface, DependencyInterface|InitializerInterface $dependency, bool $isThrow = true): static;
    public function bindConstructible(string|array $interface, string $class, bool $isThrow = true): static;
    public function bindInjectable(string|array $interface, string $class, bool $isThrow = true): static;
    public function bindObject(string|array $interface, object $object, bool $isThrow = true): static;
    public function set(string $key, mixed $value): static;
    
    public function buildContainer(ResolverInterface $resolver): ContainerInterface;
}
<?php
declare(strict_types=1);

namespace IfCastle\DI;

interface BuilderInterface
{
    public function bind(string|array $interface, DependencyInterface $dependency): static;
    public function bindConstructible(string|array $interface, string $class): static;
    public function bindInjectable(string|array $interface, string $class): static;
    public function set(string $key, mixed $value): static;
    
    public function buildContainer(ResolverInterface $resolver): ContainerInterface;
}
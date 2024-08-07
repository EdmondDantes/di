<?php
declare(strict_types=1);

namespace IfCastle\DI\Dependencies;

use IfCastle\DI\DependencyInterface;
use IfCastle\DI\InjectableInterface;

final class UseInjectableClass implements UseInjectableInterface, InjectableInterface
{
    static public string $data = '';
    
    public function injectDependencies(array $dependencies, DependencyInterface $self): static
    {
        return $this;
    }
    
    public function initializeAfterInject(): static
    {
        return $this;
    }
    
    public function someMethod2(): void
    {
        self::$data = 'someMethod2';
    }
}
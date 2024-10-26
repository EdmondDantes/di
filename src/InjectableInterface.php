<?php

declare(strict_types=1);

namespace IfCastle\DI;

interface InjectableInterface
{
    /**
     * @param array<string, mixed>  $dependencies
     * @param DependencyInterface   $self
     *
     * @return $this
     */
    public function injectDependencies(array $dependencies, DependencyInterface $self): static;
    
    public function initializeAfterInject(): static;
}

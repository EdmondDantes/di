<?php

declare(strict_types=1);

namespace IfCastle\DI;

interface InjectableInterface
{
    public function injectDependencies(array $dependencies, DependencyInterface $self): static;
    public function initializeAfterInject(): static;
}

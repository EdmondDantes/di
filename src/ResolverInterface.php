<?php

declare(strict_types=1);

namespace IfCastle\DI;

interface ResolverInterface
{
    public function canResolveDependency(DependencyInterface $dependency, ContainerInterface $container): bool;

    public function resolveDependency(DependencyInterface $dependency, ContainerInterface $container): mixed;
}

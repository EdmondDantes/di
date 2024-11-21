<?php

declare(strict_types=1);

namespace IfCastle\DI;

interface ResolverInterface
{
    public function canResolveDependency(DependencyInterface $dependency, ContainerInterface $container): bool;

    /**
     * @param array<class-string> $resolvingKeys list of classes that are currently being resolved
     */
    public function resolveDependency(
        DependencyInterface $dependency,
        ContainerInterface $container,
        string|DescriptorInterface $name,
        array $resolvingKeys = []
    ): mixed;
}

<?php

declare(strict_types=1);

namespace IfCastle\DI;

/**
 * An interface that provides a dependency based on its descriptor, container, and dependency.
 */
interface ProviderInterface
{
    public function provide(ContainerInterface $container, DescriptorInterface $descriptor, ?DependencyInterface $forDependency = null): mixed;
}

<?php

declare(strict_types=1);

namespace IfCastle\DI;

use IfCastle\DI\Exceptions\DependencyNotFound;

/**
 * Dependency container interface that behaves like a ServiceLocator
 */
interface ContainerInterface
{
    /**
     * @template Class
     * @param class-string<Class>|string|DescriptorInterface $name
     *
     * @return ($name is class-string ? Class : scalar|array<scalar>|null)
     * @throws DependencyNotFound
     */
    public function resolveDependency(string|DescriptorInterface $name, ?DependencyInterface $forDependency = null, int $stackOffset = 0): mixed;
    
    /**
     * @template Class
     *
     * @param class-string<Class>|string|DescriptorInterface    $name
     *
     * @return ($name is class-string ? Class|null : scalar|array<scalar>|null)
     */
    public function findDependency(string|DescriptorInterface $name, ?DependencyInterface $forDependency = null): mixed;
    
    /**
     * @template Class
     * @param class-string<Class>|string|DescriptorInterface    $name
     *
     * @return ($name is class-string ? Class|null : scalar|array<scalar>|null)
     */
    public function getDependencyIfInitialized(string|DescriptorInterface $name): mixed;
    
    /**
     * @param class-string|string|DescriptorInterface $key
     */
    public function hasDependency(string|DescriptorInterface $key): bool;
    
    /**
     * @param class-string|string|DescriptorInterface $key
     */
    public function findKey(string|DescriptorInterface $key): mixed;

    public function getContainerLabel(): string;
}

<?php
declare(strict_types=1);

namespace IfCastle\DI;

interface ContainerInterface
{
    public function resolveDependency(string|DescriptorInterface $name, DependencyInterface $forDependency = null): mixed;
    public function findDependency(string|DescriptorInterface $name, DependencyInterface $forDependency = null): mixed;
    public function getDependencyIfInitialized(string|DescriptorInterface $name): mixed;
    public function hasDependency(string|DescriptorInterface $key): bool;
    
    public function findKey(string|DescriptorInterface $key): mixed;
    
    public function getContainerLabel(): string;
}
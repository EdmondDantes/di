<?php
declare(strict_types=1);

namespace IfCastle\DI;

interface FactoryInterface
{
    public function create(ContainerInterface $container, DescriptorInterface $descriptor, DependencyInterface $forDependency = null): object|null;
}
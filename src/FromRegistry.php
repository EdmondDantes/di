<?php
declare(strict_types=1);

namespace IfCastle\DI;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY|Attribute::TARGET_PARAMETER)]
final class FromRegistry            extends Dependency
                                    implements FactoryInterface
{
    #[\Override]
    public function getFactory(): FactoryInterface|null
    {
        return $this;
    }
    
    #[\Override]
    public function create(
        ContainerInterface  $container,
        DescriptorInterface $descriptor,
        DependencyInterface $forDependency = null
    ): object|null
    {
        $registry                     = $container->findDependency(ComponentRegistryInterface::class);
        
        if($registry === null) {
            return null;
        }
        
        if($registry instanceof ComponentRegistryInterface === false) {
            throw new \TypeError('Registry is not an instance of '.ComponentRegistryInterface::class);
        }
        
        return $registry->findComponentConfig($this->getDependencyKey());
    }
}
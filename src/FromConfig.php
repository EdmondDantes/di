<?php
declare(strict_types=1);

namespace IfCastle\DI;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY|Attribute::TARGET_PARAMETER)]
final class FromConfig  extends Dependency
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
        $config                     = $container->findDependency(ConfigInterface::class);
        
        if($config === null) {
            return null;
        }
        
        if($config instanceof ConfigInterface === false) {
            throw new \TypeError('Config is not an instance of '.ConfigInterface::class);
        }
        
        return $config->findValue($this->key);
    }
}
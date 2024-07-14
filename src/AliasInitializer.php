<?php
declare(strict_types=1);

namespace IfCastle\DI;

/**
 * This class is used to inject dependencies into the DI container by alias of existing dependency.
 */
final class AliasInitializer        implements InitializerInterface
{
    private \WeakReference $container;
    private \WeakReference|null $dependency = null;
    
    public function __construct(readonly public string $alias, ContainerInterface $container, readonly public bool $isRequired = false)
    {
        $this->container            = \WeakReference::create($container);
    }

    #[\Override]
    public function wasCalled(): bool
    {
        return $this->dependency !== null;
    }
    
    #[\Override]
    public function executeInitializer(): mixed
    {
        if($this->dependency !== null) {
            return $this->dependency->get();
        }
        
        $container                  = $this->container->get();
        
        if(null === $container) {
            return null;
        }
        
        $dependency                 = $this->isRequired ?
                                    $container->resolveDependency($this->alias) :
                                    $container->findDependency($this->alias);
        
        $this->dependency           = \WeakReference::create($dependency);
        
        return $dependency;
    }
}
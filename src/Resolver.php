<?php
declare(strict_types=1);

namespace IfCastle\DI;

class Resolver                      implements ResolverInterface
{
    /**
     * @param ContainerInterface    $container
     * @param DescriptorInterface[] $dependencies
     * @param DependencyInterface   $forDependency
     *
     * @return mixed[]
     */
    public static function resolveDependencies(ContainerInterface $container, array $dependencies, DependencyInterface $forDependency): array
    {
        $resolvedDependencies       = [];
        
        foreach($dependencies as $descriptor) {
            
            // special case: if the dependency is already initialized for LazyLoad, we can skip the resolution
            if($descriptor->isLazy()
               && $descriptor->getFactory() === null
               && null !== ($object = $container->getDependencyIfInitialized($descriptor))) {
                $resolvedDependencies[] = $object;
                continue;
            }
            
            if(false === $descriptor->isLazy()) {
                $resolvedDependencies[] = static::resolve($container, $descriptor, $forDependency);
                continue;
            }
            
            // LazyLoad
            
            $containerRef           = \WeakReference::create($container);
            $descriptorRef          = \WeakReference::create($descriptor);
            $forDependencyRef       = \WeakReference::create($forDependency);
            
            $resolvedDependencies[] = new LazyLoader(static function () use ($containerRef, $descriptorRef, $forDependencyRef) {
                $container          = $containerRef->get();
                $descriptor         = $descriptorRef->get();
                $dependency         = $forDependencyRef->get();
                
                if($container === null || $descriptor === null || $dependency === null) {
                    throw new \Error('container, descriptor or dependency is not available');
                }
                
                return static::resolve($container, $descriptor, $dependency);
            });
        }
        
        return $resolvedDependencies;
    }
    
    public static function resolve(ContainerInterface $container, DescriptorInterface $descriptor, DependencyInterface $forDependency): object|null
    {
        $object                 = $descriptor->getFactory()?->create($container, $descriptor, $forDependency);
        
        if($object !== null) {
            return $object;
        }
        
        return $container->resolveDependency($descriptor, $forDependency);
    }
    
    #[\Override]
    public function canResolveDependency(DependencyInterface $dependency, ContainerInterface $container): bool
    {
        return $dependency instanceof ConstructibleInterface;
    }
    
    #[\Override]
    public function resolveDependency(DependencyInterface $dependency, ContainerInterface $container): mixed
    {
        $self                       = $dependency;
        
        if(false === $dependency instanceof ConstructibleInterface) {
            throw new \Error('descriptor must implement ConstructibleInterface');
        }
        
        $dependencies               = static::resolveDependencies($container, $self->getDependencyDescriptors(), $self);
        
        if($dependency->useConstructor()) {
            $className              = $dependency->getClassName();
            return new $className(...$dependencies);
        }
        
        $className                  = $dependency->getClassName();
        $object                     = new $className;
        
        if($object instanceof InjectableInterface) {
            return $object->injectDependencies($dependencies, $self)->initializeAfterInject();
        }
        
        return $object;
    }
}
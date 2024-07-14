<?php
declare(strict_types=1);

namespace IfCastle\DI;

use IfCastle\DI\Exceptions\DependencyNotFound;

class Container                     implements NestedContainerInterface
{
    private \WeakReference|ContainerInterface|null $parentContainer = null;
    
    public function __construct(protected ResolverInterface $resolver, protected array $container = [], ContainerInterface $parentContainer = null)
    {
        if(null !== $parentContainer) {
            $this->parentContainer  = $parentContainer;
        }
    }
    
    public function getParentContainer(): ContainerInterface|null
    {
        if($this->parentContainer instanceof \WeakReference) {
            return $this->parentContainer->get();
        }
        
        return $this->parentContainer;
    }
    
    public function resolveDependency(string|DescriptorInterface $name, DependencyInterface $forDependency = null): mixed
    {
        $dependency                 = $this->findDependency($name, $forDependency);
        
        if(null === $dependency) {
            throw new DependencyNotFound($name, $this, $forDependency);
        }
        
        if($dependency instanceof \Throwable) {
            throw $dependency;
        }
        
        return $dependency;
    }
    
    public function findDependency(string|DescriptorInterface $name, DependencyInterface $forDependency = null): mixed
    {
        $key                        = $this->findKey($name);
        
        if(null === $key) {
            return $this->getParentContainer()?->findDependency($name, $forDependency);
        }
        
        $dependency                 = $this->container[$key];
        
        if($dependency instanceof \Throwable) {
            return $dependency;
        }
        
        if($dependency instanceof InitializerInterface) {
            
            try {
                $this->container[$key] = $dependency->executeInitializer();
                return $this->container[$key];
            } catch (\Throwable $exception) {
                $this->container[$key] = $exception;
                return $exception;
            }
        }
        
        if(false === $dependency instanceof DependencyInterface) {
            return $dependency;
        }
        
        if($this->resolver->canResolveDependency($dependency, $this)) {
            
            try {
                $this->container[$key] = $this->resolver->resolveDependency($dependency, $this);
                return $this->container[$key];
            } catch (\Throwable $exception) {
                $this->container[$key] = $exception;
                return $exception;
            }
        }
        
        return null;
    }
    
    public function getDependencyIfInitialized(string|DescriptorInterface $name): mixed
    {
        $key                        = $this->findKey($name);
        
        if(null === $key) {
            return $this->getParentContainer()?->getDependencyIfInitialized($name);
        }
        
        $dependency                 = $this->container[$key];
        
        if($dependency instanceof \Throwable) {
            return $dependency;
        }
        
        if($dependency instanceof InitializerInterface || $dependency instanceof DependencyInterface) {
            return null;
        }
        
        return $dependency;
    }
    
    public function hasDependency(string|DescriptorInterface $key): bool
    {
        return $this->findKey($key) !== null || ($this->getParentContainer()?->hasDependency($key) ?? false);
    }
    
    public function findKey(DescriptorInterface|string $key): string|null
    {
        if(is_string($key) && array_key_exists($key, $this->container)) {
            return $key;
        } elseif (is_string($key)) {
            return null;
        }
        
        $type                   = $key->getDependencyType();
        
        foreach(array_merge([$key->getDependencyKey()], is_array($type) ? $type : [$type]) as $key) {
            if(array_key_exists($key, $this->container)) {
                return $key;
            }
        }

        return null;
    }
}
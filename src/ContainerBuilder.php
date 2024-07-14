<?php
declare(strict_types=1);

namespace IfCastle\DI;

class ContainerBuilder              implements BuilderInterface
{
    protected array $bindings       = [];
    
    public function bind(array|string $interface, DependencyInterface $dependency): static
    {
        $keys                       = is_array($interface) ? $interface : [$interface];
        $firstKey                   = array_shift($keys);
        
        if(array_key_exists($firstKey, $this->bindings)) {
            throw new \InvalidArgumentException("Interface '$firstKey' already bound");
        }
        
        $this->bindings[$firstKey]  = $dependency;
        
        foreach ($keys as $key) {
            
            if(array_key_exists($key, $this->bindings)) {
                throw new \InvalidArgumentException("Interface '$key' already bound");
            }
            
            $this->bindings[$key]    = new AliasInitializer($key);
        }
        
        return $this;
    }
    
    public function bindConstructible(array|string $interface, string $class): static
    {
        return $this->bind($interface, new ConstructibleDependency($class));
    }
    
    public function bindInjectable(array|string $interface, string $class): static
    {
        return $this->bind($interface, new ConstructibleDependency($class, false));
    }
    
    public function set(string $key, mixed $value): static
    {
        if(array_key_exists($key, $this->bindings)) {
            throw new \InvalidArgumentException("Key '$key' already defined");
        }
        
        $this->bindings[$key]       = $value;
        
        return $this;
    }
    
    public function buildContainer(ResolverInterface $resolver): ContainerInterface
    {
        $bindings                   = $this->bindings;
        $this->bindings             = [];
        
        return new Container($resolver, $bindings);
    }
}
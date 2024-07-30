<?php
declare(strict_types=1);

namespace IfCastle\DI;

class ContainerBuilder              implements BuilderInterface
{
    protected array $bindings       = [];
    
    #[\Override]
    public function isBound(string ...$keys): bool
    {
        foreach ($keys as $key) {
            if(array_key_exists($key, $this->bindings)) {
                return true;
            }
        }
        
        return false;
    }
    
    #[\Override]
    public function bind(array|string $interface, DependencyInterface|InitializerInterface $dependency, bool $isThrow = true): static
    {
        $keys                       = is_array($interface) ? $interface : [$interface];
        $firstKey                   = array_shift($keys);
        
        if(array_key_exists($firstKey, $this->bindings)) {
            if($isThrow) {
                throw new \InvalidArgumentException("Interface '$firstKey' already bound to '".$this->getKeyAsString($firstKey)."'");
            } else {
                return $this;
            }
        }
        
        $this->bindings[$firstKey]  = $dependency;
        
        foreach ($keys as $key) {
            
            if(array_key_exists($key, $this->bindings)) {
                if($isThrow) {
                    throw new \InvalidArgumentException("Interface '$key' already bound to '".$this->getKeyAsString($key)."'");
                } else {
                    continue;
                }
            }
            
            $this->bindings[$key]    = new AliasInitializer($firstKey);
        }
        
        return $this;
    }
    
    #[\Override]
    public function bindConstructible(array|string $interface, string $class, bool $isThrow = true): static
    {
        return $this->bind($interface, new ConstructibleDependency($class), $isThrow);
    }
    
    #[\Override]
    public function bindInjectable(array|string $interface, string $class, bool $isThrow = true): static
    {
        return $this->bind($interface, new ConstructibleDependency($class, false));
    }
    
    #[\Override]
    public function bindObject(array|string $interface, object $object, bool $isThrow = true): static
    {
        if($object instanceof InitializerInterface || $object instanceof DependencyInterface) {
            throw new \InvalidArgumentException('Object cannot be used as dependency or initializer');
        }
        
        foreach (is_array($interface) ? $interface : [$interface] as $key) {
            
            if(array_key_exists($key, $this->bindings)) {
                if($isThrow) {
                    throw new \InvalidArgumentException("Interface '$key' already bound to '".$this->getKeyAsString($key)."'");
                } else {
                    continue;
                }
            }
            
            $this->bindings[$key]    = $object;
        }
        
        return $this;
    }
    
    #[\Override]
    public function set(string $key, mixed $value): static
    {
        if(array_key_exists($key, $this->bindings)) {
            throw new \InvalidArgumentException("Key '$key' already defined");
        }
        
        $this->bindings[$key]       = $value;
        
        return $this;
    }
    
    #[\Override]
    public function buildContainer(ResolverInterface $resolver): ContainerInterface
    {
        $bindings                   = $this->bindings;
        $this->bindings             = [];
        
        return new Container($resolver, $bindings);
    }
    
    #[\Override]
    public function getKeyAsString(string $key): string
    {
        if(false === array_key_exists($key, $this->bindings)) {
            return 'undefined';
        }
        
        $value                      = $this->bindings[$key];
        
        if ($value instanceof AliasInitializer) {
            
            if(array_key_exists($value->alias, $this->bindings)) {
                return 'alias: '.$value->alias.' -> '.$this->getKeyAsString($value->alias);
            }
            
            return 'alias: '.$value->alias.' -> undefined';
        }
        
        if($value instanceof ConstructibleInterface) {
            return 'dependency: '.$value->getClassName();
        } elseif(is_object($value)) {
            return 'object: '.get_class($value);
        } else {
            return 'type: '.get_debug_type($value);
        }
    }
}
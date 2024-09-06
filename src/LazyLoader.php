<?php
declare(strict_types=1);

namespace IfCastle\DI;

final class LazyLoader
{
    /**
     * @var callable|null
     */
    private $initializer;
    private mixed $afterHandler;
    
    final public function __construct(callable $initializer)
    {
        $this->initializer  = $initializer;
    }
    
    final public function setAfterHandler(callable $handler): void
    {
        $this->afterHandler = $handler;
    }
    
    final public function __call($method = null, $arguments = [])
    {
        // destroy initializer
        $initializer        = $this->initializer;
        $this->initializer  = null;
        $afterHandler       = $this->afterHandler;
        $this->afterHandler = null;
        
        if($initializer === null) {
            throw new \BadMethodCallException('LazyLoader erroneous call with destroyed $initializer');
        }
        
        $object             = call_user_func($initializer);
        
        if($afterHandler !== null) {
            call_user_func($afterHandler, $object);
        }
        
        if($method === null || $object === null)
        {
            return null;
        }
        
        return call_user_func_array([$object, $method], $arguments);
    }
}
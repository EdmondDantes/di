<?php
declare(strict_types=1);

namespace IfCastle\DI;

final class LazyLoader
{
    /**
     * @var callable|null
     */
    private $initializer;
    
    final public function __construct(callable $initializer)
    {
        $this->initializer  = $initializer;
    }
    
    final public function __call($method = null, $arguments = [])
    {
        // destroy initializer
        $initializer        = $this->initializer;
        $this->initializer  = null;
        
        if($initializer === null) {
            throw new \BadMethodCallException('LazyLoader erroneous call with destroyed $initializer');
        }
        
        $object             = call_user_func($initializer);
        
        if($method === null || $object === null)
        {
            return null;
        }
        
        return call_user_func_array([$object, $method], $arguments);
    }
}
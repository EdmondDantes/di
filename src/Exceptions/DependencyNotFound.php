<?php
declare(strict_types=1);

namespace IfCastle\DI\Exceptions;

use IfCastle\DI\ContainerInterface;
use IfCastle\DI\DependencyInterface;
use IfCastle\DI\DescriptorInterface;

class DependencyNotFound            extends \Exception
{
    public function __construct(string|DescriptorInterface $name,
                                ContainerInterface $container,
                                DependencyInterface $forDependency = null,
                                ?\Throwable $previous = null
    )
    {
        
        parent::__construct($this->generateMessage($name, $container, $forDependency), 0, $previous);
    }
    
    protected function generateMessage(string|DescriptorInterface $name, ContainerInterface $container, DependencyInterface $forDependency = null): string
    {
        $requiredBy                 = '';
        $file                       = '';
        $line                       = '';
        $key                        = $name instanceof DescriptorInterface ? $name->getDependencyKey() : $name;
        $forDependency              = $forDependency !== null ? $forDependency::class : '';
        $container                  = $container->getContainerLabel();
        
        $backtrace                  = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 4);
        
        if(isset($backtrace[3]) && $backtrace[3] !== []) {
            $requiredBy             = $backtrace[3]['class'] ?? ''.$backtrace[3]['type'] ?? ''.$backtrace[3]['function'] ?? '';
            $file                   = $backtrace[2]['file'] ?? '';
            $line                   = $backtrace[2]['line'] ?? '';
        }

        return "The dependency '$key' is not found in container '$container', required at $file:$line by $requiredBy, '$forDependency'";
    }
    
}
<?php
declare(strict_types=1);

namespace IfCastle\DI;

final readonly class ConstructibleDependency implements DependencyInterface, ConstructibleInterface
{
    public function __construct(private string $className, private bool $useConstructor = true) {}
    
    
    public function getClassName(): string
    {
        return $this->className;
    }
    
    public function useConstructor(): bool
    {
        return $this->useConstructor;
    }
    
    public function getDependencyDescriptors(): array
    {
        return [];
    }
}
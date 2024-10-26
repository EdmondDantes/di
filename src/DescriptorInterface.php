<?php

declare(strict_types=1);

namespace IfCastle\DI;

interface DescriptorInterface
{
    public function getDependencyKey(): string;
    
    public function getDependencyProperty(): string;
    
    /**
     * @return string|string[]|null
     */
    public function getDependencyType(): string|array|null;
    
    public function isRequired(): bool;
    
    public function isLazy(): bool;
    
    public function getFactory(): FactoryInterface|null;
    
    public function hasDefaultValue(): bool;
    
    public function getDefaultValue(): mixed;
}

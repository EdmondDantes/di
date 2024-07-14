<?php
declare(strict_types=1);

namespace IfCastle\DI\Attributes;

use Attribute;
use IfCastle\DI\DescriptorInterface;
use IfCastle\DI\FactoryInterface;

#[Attribute(Attribute::TARGET_PROPERTY|Attribute::TARGET_PARAMETER)]
class Dependency implements DescriptorInterface
{
    public function __construct(
        public string $key               = '',
        public string|array|null $type   = null,
        public bool $isRequired          = true,
        public bool $isLazy              = false,
        public string $property          = '',
    ) {}
    
    public function getDependencyKey(): string
    {
        return $this->key;
    }
    
    public function getDependencyProperty(): string
    {
        return $this->property;
    }
    
    public function getDependencyType(): string|array|null
    {
        return $this->type;
    }
    
    public function isRequired(): bool
    {
        return $this->isRequired;
    }
    
    public function isLazy(): bool
    {
        return $this->isLazy;
    }
    
    public function getFactory(): FactoryInterface|null
    {
        return null;
    }
}
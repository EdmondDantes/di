<?php
declare(strict_types=1);

namespace IfCastle\DI;

interface DependencyInterface
{
    /**
     * @return DescriptorInterface[]
     */
    public function getDependencyDescriptors(): array;
}
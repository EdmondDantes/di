<?php
declare(strict_types=1);

namespace IfCastle\DI;

interface ConfigurableFromArrayInterface
{
    public function applyConfigArray(array $config): void;
}
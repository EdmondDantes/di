<?php
declare(strict_types=1);

namespace IfCastle\DI;

/**
 * Interface ConfigurableFromArrayInterface
 *
 * Used to apply configuration from an array.
 *
 * @package IfCastle\DI
 */
interface ConfigurableFromArrayInterface
{
    public function configureFromArray(array $config): void;
}
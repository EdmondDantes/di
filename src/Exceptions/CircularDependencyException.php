<?php

declare(strict_types=1);

namespace IfCastle\DI\Exceptions;

use IfCastle\DI\ContainerInterface;
use IfCastle\DI\DescriptorInterface;

final class CircularDependencyException extends \Exception
{
    /**
     * @param array<string>              $resolvingKeys
     */
    public function __construct(string|DescriptorInterface $name, ContainerInterface $container, array $resolvingKeys)
    {
        $name                       = $name instanceof DescriptorInterface ? $name->getDependencyKey() : $name;
        $container                  = $container::class;
        $resolvingKeys              = \implode(' -> ', $resolvingKeys);

        parent::__construct('A circular dependency '.$name.' was detected,'
                            .' but attempting to resolve it results a Proxy object. '
                            . ' container: ' . $container . ' (resolving keys: ' . $resolvingKeys . ')'
        );
    }
}

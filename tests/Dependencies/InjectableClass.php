<?php

declare(strict_types=1);

namespace IfCastle\DI\Dependencies;

use IfCastle\DI\Dependency;
use IfCastle\DI\InjectableInterface;
use IfCastle\DI\InjectorTrait;
use IfCastle\DI\LazyLoader;

final class InjectableClass implements InjectableInterface
{
    use InjectorTrait;

    #[Dependency]
    protected UseConstructorInterface $required;

    #[Dependency]
    protected UseConstructorInterface|null $optional;

    #[Dependency]
    protected UseConstructorInterface|LazyLoader $lazy;

    protected string $data = '';
}

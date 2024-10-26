<?php

declare(strict_types=1);

namespace IfCastle\DI;

use IfCastle\DI\Exceptions\DependencyNotFound;

/**
 * @template T
 *
 * This class is used to inject dependencies into the DI container by alias of existing dependency.
 */
final class AliasInitializer implements InitializerInterface
{
    /**
     * @var \WeakReference<object>|null
     */
    private \WeakReference|null $dependency = null;

    public function __construct(readonly public string $alias, readonly public bool $isRequired = false) {}

    #[\Override]
    public function wasCalled(): bool
    {
        return $this->dependency !== null;
    }

    /**
     * @return T|null
     * @throws DependencyNotFound
     */
    #[\Override]
    public function executeInitializer(?ContainerInterface $container = null): mixed
    {
        if ($this->dependency !== null) {
            return $this->dependency->get();
        }

        if (null === $container) {
            return null;
        }

        $dependency                 = $this->isRequired ?
                                    $container->resolveDependency($this->alias) :
                                    $container->findDependency($this->alias);

        $this->dependency           = \WeakReference::create($dependency);

        return $dependency;
    }
}

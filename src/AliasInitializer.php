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
    private bool $wasCalled = false;
    /**
     * @var \WeakReference<object>|scalar|array<scalar>|null
     */
    private \WeakReference|int|string|float|bool|array|null $dependency = null;

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
        if($this->wasCalled) {
            
            if ($this->dependency instanceof \WeakReference) {
                return $this->dependency->get();
            }

            return $this->dependency;
        }
        
        if (null === $container) {
            return null;
        }
        
        $this->wasCalled            = true;

        $dependency                 = $this->isRequired ?
                                    $container->resolveDependency($this->alias) :
                                    $container->findDependency($this->alias);

        if(is_object($dependency)) {
            $this->dependency       = \WeakReference::create($dependency);
        } else {
            $this->dependency       = $dependency;
        }

        return $dependency;
    }
}

<?php
declare(strict_types=1);

namespace IfCastle\DI;

abstract class InitializerAbstract implements InitializerInterface
{
    private bool $wasCalled = false;
    
    #[\Override]
    public function wasCalled(): bool
    {
        return $this->wasCalled;
    }
    
    #[\Override]
    public function executeInitializer(ContainerInterface $container = null): mixed
    {
        $this->wasCalled = true;
        return $this->initialize($container);
    }
    
    abstract protected function initialize(ContainerInterface $container): mixed;
}
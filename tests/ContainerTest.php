<?php
declare(strict_types=1);

namespace IfCastle\DI;

use IfCastle\DI\Dependencies\UseConstructorClass;
use IfCastle\DI\Dependencies\UseConstructorInterface;
use IfCastle\DI\Dependencies\UseInjectableClass;
use IfCastle\DI\Dependencies\UseInjectableInterface;
use IfCastle\DI\Exceptions\DependencyNotFound;
use PHPUnit\Framework\TestCase;

class ContainerTest                 extends TestCase
{
    protected Container $container;
    
    public function setUp(): void
    {
        $builder                    = new ContainerBuilder;
        $builder->bindConstructible([UseConstructorInterface::class, 'alias1'], UseConstructorClass::class);
        $builder->bindInjectable([UseInjectableInterface::class, 'alias2'], UseInjectableClass::class);
        
        $this->container            = $builder->buildContainer(new Resolver);
    }
    
    public function testResolveDependencyByKey(): void
    {
        $class1                     = $this->container->resolveDependency(UseConstructorInterface::class);
        $this->assertInstanceOf(UseConstructorClass::class, $class1);
        
        $class2                     = $this->container->resolveDependency(UseInjectableInterface::class);
        $this->assertInstanceOf(UseInjectableClass::class, $class2);

        $this->assertEquals($class1, $this->container->resolveDependency('alias1'));
        $this->assertEquals($class2, $this->container->resolveDependency('alias2'));
    }
    
    public function testDependencyNotFound(): void
    {
        $this->expectException(DependencyNotFound::class);
        $this->container->resolveDependency('non-existent');
    }
}

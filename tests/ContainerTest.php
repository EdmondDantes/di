<?php
declare(strict_types=1);

namespace IfCastle\DI;

use IfCastle\DI\Dependencies\ClassWithNoExistDependency;
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
        $builder->bindConstructible('wrong_dependency', ClassWithNoExistDependency::class);
        
        $this->container            = $builder->buildContainer(new Resolver);
    }
    
    /**
     * @throws DependencyNotFound
     */
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
    
    public function testDependencyNotFoundMessage(): void
    {
        try {
            $this->container->resolveDependency('non-existent');
        } catch (DependencyNotFound $exception) {
            $this->assertStringContainsString(__FILE__.':'.__LINE__ - 2, $exception->getMessage());
        }
    }
    
    public function testDependencyNotFoundMessageForOtherDependency(): void
    {
        try {
            $this->container->resolveDependency('wrong_dependency');
        } catch (DependencyNotFound $exception) {
            $this->assertStringContainsString(__FILE__.':'.__LINE__ - 2, $exception->getMessage());
        }
    }
    
    /**
     * @throws DependencyNotFound
     */
    public function testWeakReference(): void
    {
        $object                     = new \stdClass;
        $container                  = new Container(new Resolver, ['stdClass' => \WeakReference::create($object)]);
        
        $this->assertEquals($object, $container->resolveDependency('stdClass'));
    }
}

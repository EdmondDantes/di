<?php
declare(strict_types=1);

namespace IfCastle\DI;

use IfCastle\DI\Dependencies\Test;
use PHPUnit\Framework\TestCase;

class ContainerBuilderTest extends TestCase
{
    public function testIsBound(): void
    {
        $builder                    = new ContainerBuilder();
        $builder->bind('test', $this->createMock(DependencyInterface::class));
        $this->assertTrue($builder->isBound('test'));
        $this->assertFalse($builder->isBound('nonexistent'));
        
        $container                  = $builder->buildContainer($this->createMock(ResolverInterface::class));
        $this->assertTrue($container->findKey('test') !== null);
    }
    
    public function testBind(): void
    {
        $builder                    = new ContainerBuilder();
        $dependency                 = $this->createMock(DependencyInterface::class);
        $builder->bind('test', $dependency);
        $this->assertTrue($builder->isBound('test'));
        
        $container                  = $builder->buildContainer($this->createMock(ResolverInterface::class));
        $this->assertTrue($container->findKey('test') !== null);
    }
    
    public function testBindConstructible(): void
    {
        $builder                    = new ContainerBuilder();
        $builder->bindConstructible('test', Test::class);
        $this->assertTrue($builder->isBound('test'));
        
        $container                  = $builder->buildContainer($this->createMock(ResolverInterface::class));
        $this->assertTrue($container->findKey('test') !== null);
    }
    
    public function testBindInjectable(): void
    {
        $builder                    = new ContainerBuilder();
        $builder->bindInjectable('test', Test::class);
        $this->assertTrue($builder->isBound('test'));
        
        $container = $builder->buildContainer($this->createMock(ResolverInterface::class));
        $this->assertTrue($container->findKey('test') !== null);
    }
    
    public function testBindObject(): void
    {
        $builder                    = new ContainerBuilder();
        $object                     = new \stdClass();
        $builder->bindObject('test', $object);
        $this->assertTrue($builder->isBound('test'));
        
        $container                  = $builder->buildContainer($this->createMock(ResolverInterface::class));
        $this->assertTrue($container->findKey('test') !== null);
    }
    
    public function testSet()
    {
        $builder                    = new ContainerBuilder();
        $builder->set('test', 'value');
        $this->assertTrue($builder->isBound('test'));
        
        $container                  = $builder->buildContainer($this->createMock(ResolverInterface::class));
        $this->assertEquals('value', $container->resolveDependency('test'));
    }
    
    public function testBuildContainer()
    {
        $builder                    = new ContainerBuilder();
        $resolver                   = $this->createMock(ResolverInterface::class);
        $container                  = $builder->buildContainer($resolver);
        
        $this->assertInstanceOf(ContainerInterface::class, $container);
    }
    
    public function testGetKeyAsString()
    {
        $builder                    = new ContainerBuilder();
        
        $builder->bind('test', $this->createMock(DependencyInterface::class));
        $this->assertStringContainsString('object', $builder->getKeyAsString('test'));
    }
}
<?php
declare(strict_types=1);

namespace IfCastle\DI;

use IfCastle\DI\Dependencies\ClassWithScalarDependencies;
use IfCastle\DI\Dependencies\InjectableClass;
use IfCastle\DI\Dependencies\Test;
use IfCastle\DI\Dependencies\UseConstructorInterface;
use PHPUnit\Framework\TestCase;

class AttributesToDescriptorsTest extends TestCase
{
    public function testParameterToDescriptor(): void
    {
        $descriptors                = AttributesToDescriptors::readDescriptors(Test::class);
        
        $this->assertIsArray($descriptors);
        
        $descriptor                 = $descriptors[0];
        
        $this->assertInstanceOf(DescriptorInterface::class, $descriptor);
        $this->assertInstanceOf(Dependency::class, $descriptor);
        $this->assertEquals('some', $descriptor->key);
        $this->assertEquals(UseConstructorInterface::class, $descriptor->type);
        $this->assertTrue($descriptor->isRequired);
        $this->assertEquals('', $descriptor->property);
        $this->assertFalse($descriptor->isLazy);
    }
    
    public function testPropertyToDescriptor(): void
    {
        $descriptors                = AttributesToDescriptors::readDescriptors(InjectableClass::class);
        
        $this->assertIsArray($descriptors);
        $this->assertCount(3, $descriptors);
        
        $required                   = $descriptors[0];
        
        $this->assertInstanceOf(DescriptorInterface::class, $required);
        $this->assertInstanceOf(Dependency::class, $required);
        $this->assertEquals('required', $required->key);
        $this->assertEquals(UseConstructorInterface::class, $required->type);
        $this->assertTrue($required->isRequired);
        $this->assertEquals('required', $required->property);
        $this->assertFalse($required->isLazy);
        
        $optional                   = $descriptors[1];
        
        $this->assertInstanceOf(DescriptorInterface::class, $optional);
        $this->assertInstanceOf(Dependency::class, $optional);
        $this->assertEquals('optional', $optional->key);
        $this->assertEquals(UseConstructorInterface::class, $optional->type);
        $this->assertFalse($optional->isRequired);
        $this->assertEquals('optional', $optional->property);
        $this->assertFalse($optional->isLazy);
        
        $lazy                       = $descriptors[2];
        
        $this->assertInstanceOf(DescriptorInterface::class, $lazy);
        $this->assertInstanceOf(Dependency::class, $lazy);
        $this->assertEquals('lazy', $lazy->key);
        $this->assertEquals([UseConstructorInterface::class, LazyLoader::class], $lazy->type);
        $this->assertTrue($lazy->isRequired);
        $this->assertEquals('lazy', $lazy->property);
        $this->assertTrue($lazy->isLazy);
    }
    
    public function testScalarParameters(): void
    {
        $descriptors                = AttributesToDescriptors::readDescriptors(ClassWithScalarDependencies::class);
        
        $this->assertIsArray($descriptors);
        
        foreach ($descriptors as $descriptor) {
            $this->assertInstanceOf(DescriptorInterface::class, $descriptor);
            $this->assertInstanceOf(Dependency::class, $descriptor);
            $this->assertInstanceOf(FromConfig::class, $descriptor);
            
            $this->assertStringContainsString('class_with_scalar_dependencies', $descriptor->getKey());
        }
    }
}
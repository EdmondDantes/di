<?php
declare(strict_types=1);

namespace IfCastle\DI;

use IfCastle\DI\Attributes\Dependency;
use IfCastle\DI\Exceptions\InjectionNotPossible;

class AttributesToDescriptors
{
    public static function readDescriptors(object|string $object): array
    {
        $reflection                 = new \ReflectionClass($object);
        
        if(false === $reflection->implementsInterface(InjectableInterface::class)) {
            
            $constructor            = $reflection->getConstructor();
            
            
            if($constructor === null)
            {
                return [];
            }
            
            $descriptors            = [];
            
            foreach ($constructor->getParameters() as $parameter) {
                $descriptors[]          = self::parameterToDescriptor($parameter, $object);
            }
            
            return $descriptors;
        }
        
        $descriptors                = [];
        
        foreach ($reflection->getProperties(\ReflectionProperty::IS_PROTECTED | \ReflectionProperty::IS_PUBLIC)
                 as $property) {
            
            $descriptors[]          = self::propertyToDescriptor($property, $object);
        }
        
        return $descriptors;
    }
    
    protected static function parameterToDescriptor(\ReflectionParameter $parameter, object|string $object): DescriptorInterface
    {
        $attributes             = $parameter->getAttributes(DescriptorInterface::class, \ReflectionAttribute::IS_INSTANCEOF);
        
        if(!empty($attributes)) {
            $attribute          = $attributes[0];
            $descriptor         = $attribute->newInstance();
        } else {
            $descriptor         = new Dependency;
        }
        
        if($descriptor instanceof DescriptorInterface === false) {
            throw new \Error('Attribute is not an instance of Dependency');
        }
        
        if(false === $descriptor instanceof Dependency) {
            return $descriptor;
        }
        
        if($descriptor->key === '') {
            $descriptor->key    = $parameter->getName();
        }
        
        if($descriptor->type === null) {
            $descriptor->type   = self::defineType($parameter->getType(), $object);
        }
        
        $descriptor->isRequired = false === $parameter->allowsNull() && $parameter->isOptional() === false;
        
        $descriptor->isLazy     = is_array($descriptor->type) && in_array(LazyLoader::class, $descriptor->type, true);
        
        return $descriptor;
    }
    
    protected static function propertyToDescriptor(\ReflectionProperty $property, object|string $object): DescriptorInterface
    {
        $attributes             = $property->getAttributes(DescriptorInterface::class, \ReflectionAttribute::IS_INSTANCEOF);
        
        if(!empty($attributes)) {
            $attribute          = $attributes[0];
            $descriptor         = $attribute->newInstance();
        } else {
            $descriptor         = new Dependency;
        }
        
        if($descriptor instanceof DescriptorInterface === false) {
            throw new \Error('Attribute is not an instance of Dependency');
        }
        
        if($descriptor->key === '') {
            $descriptor->key    = $property->getName();
        }
        
        if($descriptor->type === null) {
            $descriptor->type   = self::defineType($property->getType(), $object);
        }
        
        $descriptor->isRequired = false === $property->hasDefaultValue() || $property->getType()?->allowsNull() === true;
        $descriptor->isLazy     = is_array($descriptor->type) && in_array(LazyLoader::class, $descriptor->type, true);
        
        return $descriptor;
    }
    
    /**
     * @throws InjectionNotPossible
     */
    protected static function defineType(mixed $type, object|string $object): string|array|null
    {
        if($type instanceof \ReflectionUnionType) {
            return self::defineUnionType($type, $object);
        }
        
        if($type instanceof \ReflectionNamedType) {
            return self::defineNamedType($type, $object);
        }
        
        if($type instanceof \ReflectionIntersectionType) {
            return self::defineIntersectionType($type, $object);
        }
        
        return null;
    }
    
    /**
     * @throws InjectionNotPossible
     */
    protected static function defineNamedType(\ReflectionNamedType $type, object|string $object): string
    {
        if($type->isBuiltin()) {
            return match ($type->getName()) {
                'null', 'int', 'float', 'string', 'bool', 'array'
                            => $type->getName(),
                default     => throw new InjectionNotPossible($object, $type->getName(), 'object or scalar type')
            };
        }
        
        return $type->getName();
    }
    
    /**
     * @throws InjectionNotPossible
     */
    protected static function defineUnionType(\ReflectionUnionType $unionType, object|string $object): array
    {
        $types                  = [];
        
        foreach ($unionType->getTypes() as $type) {
            if($type instanceof \ReflectionNamedType) {
                $types[]        = self::defineNamedType($type, $object);
            } else {
                throw new InjectionNotPossible($object, $type->getName(), 'object');
            }
        }
        
        return $types;
    }
    
    /**
     * Returns the first named type from the intersection type.
     *
     * @throws InjectionNotPossible
     */
    protected static function defineIntersectionType(\ReflectionIntersectionType $intersectionType, object|string $object): string
    {
        foreach ($intersectionType->getTypes() as $type)
        {
            if($type instanceof \ReflectionNamedType) {
                return self::defineNamedType($type, $object);
            }
        }
        
        throw new InjectionNotPossible($object, 'intersection type', 'object');
    }
}
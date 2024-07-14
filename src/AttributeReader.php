<?php
declare(strict_types=1);

namespace IfCastle\DI;

use IfCastle\DI\Exceptions\InjectionNotPossible;

class AttributeReader
{
    public static function readDescriptors(object|string $object): array
    {
        $reflection                 = new \ReflectionClass($object);
        
        if(false === $reflection->implementsInterface(InjectableInterface::class)) {
            
            $constructor                = $reflection->getConstructor();
            
            
            if($constructor === null)
            {
                return [];
            }
            
            $descriptors                = [];
            
            foreach ($constructor->getParameters() as $parameter) {
                $descriptors[]          = self::parameterToDescriptor($parameter);
            }
            
            return $descriptors;
        }
        
        $descriptors                    = [];
        
        foreach ($reflection->getProperties(\ReflectionProperty::IS_PROTECTED | \ReflectionProperty::IS_PUBLIC)
                 as $property) {
            
            $descriptors[]              = self::propertyToDescriptor($property);
        }
        
        return $descriptors;
    }
    
    protected static function parameterToDescriptor(\ReflectionParameter $parameter, object|string $object): DescriptorInterface
    {
        $attributes             = $parameter->getAttributes(DescriptorInterface::class, \ReflectionAttribute::IS_INSTANCEOF);
        
        if(empty($attributes)) {
            throw new \Error('Parameter is not annotated with Dependency');
        }
        
        $attribute              = $attributes[0];
        $descriptor             = $attribute->newInstance();
        
        if($descriptor instanceof DescriptorInterface === false) {
            throw new \Error('Attribute is not an instance of Dependency');
        }
        
        $key                    = $descriptor->getDependencyKey() ?? $parameter->getName();
        $type                   = $descriptor->getDependencyType() ?? self::defineType($parameter->getType(), $object);
        $isRequired             = false === $parameter->allowsNull() && $parameter->isOptional() === false;
        $isLazyLoad             = $descriptor->isLazy();
        $isWeakReference        = $parameter->getType()?->getName() === \WeakReference::class;
        $factory                = $descriptor->getFactory();
    }
    
    protected static function propertyToDescriptor(\ReflectionProperty $property): DescriptorInterface
    {
    
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
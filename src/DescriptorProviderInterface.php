<?php

declare(strict_types=1);

namespace IfCastle\DI;

/**
 * ## DescriptorProviderInterface
 *
 * The interface describes a way to modify the behavior of the ContainerBuilder.
 * By implementing the provideDescriptor method,
 * you can independently control the process of building a dependency descriptor.
 *
 */
interface DescriptorProviderInterface
{
    /**
     * Provide dependency descriptor for target reflection.
     *
     * @param \ReflectionClass<object>                 $reflectionClass     Target class reflection.
     * @param \ReflectionParameter|\ReflectionProperty $reflectionTarget    Target reflection.
     * @param object|string                            $object              Target object.
     *
     * @return DescriptorInterface
     */
    public static function provideDescriptor(
        \ReflectionClass     $reflectionClass,
        \ReflectionParameter|\ReflectionProperty $reflectionTarget,
        object|string        $object
    ): DescriptorInterface;
}
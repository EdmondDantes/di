<?php

declare(strict_types=1);

namespace IfCastle\DI;

use IfCastle\DI\Exceptions\CircularDependencyException;
use IfCastle\DI\Exceptions\DependencyNotFound;
use IfCastle\DI\Exceptions\MaxResolutionDepthException;

class Resolver implements ResolverInterface
{
    /**
     * @param DescriptorInterface[] $dependencies
     * @param array<class-string> $resolvingKeys list of classes that are currently being resolved
     *
     * @return mixed[]
     * @throws DependencyNotFound
     */
    public static function resolveDependencies(
        ContainerInterface $container,
        array $dependencies,
        DependencyInterface $forDependency,
        array $resolvingKeys        = [],
    ): array {
        $resolvedDependencies       = [];
        
        foreach ($dependencies as $descriptor) {

            // special case: if the dependency is already initialized for LazyLoad, we can skip the resolution
            if ($descriptor->isLazy()
               && $descriptor->getProvider() === null
               && null !== ($object = $container->getDependencyIfInitialized($descriptor))) {
                $resolvedDependencies[] = $object;
                continue;
            }

            if (false === $descriptor->isLazy()) {
                $resolvedDependencies[] = static::resolve($container, $descriptor, $forDependency, 0, $resolvingKeys);
                continue;
            }

            // LazyLoad

            $containerRef           = \WeakReference::create($container);
            
            $reflector              = new \ReflectionClass($descriptor->getDependencyType());

            $resolvedDependencies[] = new LazyLoader(static function () use ($containerRef, $descriptor, $forDependency) {
                $container          = $containerRef->get();

                if ($container === null) {
                    throw new \Error('container, descriptor or dependency is not available');
                }

                return static::resolve($container, $descriptor, $forDependency);
            });
        }

        return $resolvedDependencies;
    }

    /**
     * @param array<class-string> $resolvingKeys list of classes that are currently being resolved
     *
     * @throws DependencyNotFound
     */
    public static function resolve(
        ContainerInterface  $container,
        DescriptorInterface $descriptor,
        DependencyInterface $forDependency,
        int                 $stackOffset = 0,
        array               $resolvingKeys = [],
    ): mixed {
        $object                 = $descriptor->getProvider()?->provide($container, $descriptor, $forDependency, $resolvingKeys);

        if ($object !== null) {
            return $object;
        }

        if ($descriptor->getProvider() !== null) {

            if ($descriptor->hasDefaultValue()) {
                return $descriptor->getDefaultValue();
            }

            if ($descriptor->isRequired()) {
                throw new DependencyNotFound($descriptor, $container, $forDependency, $stackOffset + 4);
            }

            return null;
        }

        return $container->resolveDependency($descriptor, $forDependency, $stackOffset + 5, $resolvingKeys);
    }

    #[\Override]
    public function canResolveDependency(DependencyInterface $dependency, ContainerInterface $container): bool
    {
        return $dependency instanceof ConstructibleInterface;
    }

    /**
     * @throws \ReflectionException
     * @throws DependencyNotFound
     * @throws MaxResolutionDepthException
     */
    #[\Override]
    public function resolveDependency(
        DependencyInterface $dependency,
        ContainerInterface $container,
        string|DescriptorInterface  $name,
        array $resolvingKeys = [],
    ): mixed {
        $self                       = $dependency;

        if (false === $dependency instanceof ConstructibleInterface) {
            throw new \Error('descriptor must implement ConstructibleInterface');
        }

        // Circular dependency check
        if (\in_array(\is_string($name) ? $name : $name->getDependencyKey(), $resolvingKeys, true)) {

            $containerRef           = \WeakReference::create($container);
            $resolverRef            = \WeakReference::create($this);

            //
            // Use Proxy to avoid circular dependencies.
            // If a circular dependency resolution is detected, we stop the resolution process
            // and return a Proxy object that will later point to the actual dependency.
            //
            $reflection             = new \ReflectionClass($dependency->getClassName());
            $proxyObject            = $reflection->newLazyProxy(
                static function () use ($containerRef, $resolverRef, $dependency, $name, $reflection, $resolvingKeys) {

                    $container      = $containerRef->get();
                    $resolver       = $resolverRef->get();

                    if ($container === null || $resolver === null) {
                        return null;
                    }

                    $result         = $resolver->resolveDependency($dependency, $container, $name);

                    if ($reflection->isUninitializedLazyObject($result)) {
                        throw new CircularDependencyException($name, $container, $resolvingKeys);
                    }

                    return $result;
                });

            return $proxyObject;
        }

        $resolvingKeys[]            = \is_string($name) ? $name : $name->getDependencyKey();

        if (\count($resolvingKeys) > 32) {
            throw new MaxResolutionDepthException(32, $resolvingKeys);
        }

        $dependencies               = static::resolveDependencies($container, $self->getDependencyDescriptors(), $self, $resolvingKeys);

        if ($dependency->useConstructor()) {
            $className              = $dependency->getClassName();
            return new $className(...$dependencies);
        }

        $className                  = $dependency->getClassName();
        $object                     = new $className();

        if ($object instanceof InjectableInterface) {
            return $object->injectDependencies($dependencies, $self)->initializeAfterInject();
        } elseif ($object instanceof AutoResolverInterface) {
            $object->resolveDependencies($container);
        }

        return $object;
    }
}

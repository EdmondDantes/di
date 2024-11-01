<?php

declare(strict_types=1);

namespace IfCastle\DI;

use IfCastle\DI\Exceptions\DependencyNotFound;

class Resolver implements ResolverInterface
{
    /**
     * @param DescriptorInterface[] $dependencies
     *
     * @return mixed[]
     * @throws DependencyNotFound
     */
    public static function resolveDependencies(ContainerInterface $container, array $dependencies, DependencyInterface $forDependency): array
    {
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
                $resolvedDependencies[] = static::resolve($container, $descriptor, $forDependency);
                continue;
            }

            // LazyLoad

            $containerRef           = \WeakReference::create($container);

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
     * @throws DependencyNotFound
     */
    public static function resolve(
        ContainerInterface  $container,
        DescriptorInterface $descriptor,
        DependencyInterface $forDependency,
        int                 $stackOffset = 0
    ): mixed {
        $object                 = $descriptor->getProvider()?->provide($container, $descriptor, $forDependency);

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

        return $container->resolveDependency($descriptor, $forDependency, $stackOffset + 5);
    }

    #[\Override]
    public function canResolveDependency(DependencyInterface $dependency, ContainerInterface $container): bool
    {
        return $dependency instanceof ConstructibleInterface;
    }

    #[\Override]
    public function resolveDependency(DependencyInterface $dependency, ContainerInterface $container): mixed
    {
        $self                       = $dependency;

        if (false === $dependency instanceof ConstructibleInterface) {
            throw new \Error('descriptor must implement ConstructibleInterface');
        }

        $dependencies               = static::resolveDependencies($container, $self->getDependencyDescriptors(), $self);

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

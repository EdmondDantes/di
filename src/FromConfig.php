<?php

declare(strict_types=1);

namespace IfCastle\DI;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
final class FromConfig extends Dependency implements FactoryInterface
{
    private string $section = '';

    #[\Override]
    public function getFactory(): FactoryInterface|null
    {
        return $this;
    }

    public function defineSection(string $section): void
    {
        $this->section              = $section;
    }

    public function getKey(): string
    {
        if ($this->section !== '') {
            return $this->section . '.' . $this->key;
        }

        return $this->key;
    }

    #[\Override]
    public function create(
        ContainerInterface  $container,
        DescriptorInterface $descriptor,
        ?DependencyInterface $forDependency = null
    ): object|null {
        $config                     = $container->findDependency(ConfigInterface::class);

        if ($config === null) {
            return null;
        }

        if ($config instanceof ConfigInterface === false) {
            throw new \TypeError('Config is not an instance of ' . ConfigInterface::class);
        }

        return $config->findValue($this->getKey());
    }
}

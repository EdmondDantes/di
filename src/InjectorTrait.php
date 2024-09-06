<?php
declare(strict_types=1);

namespace IfCastle\DI;

trait InjectorTrait
{
    public function injectDependencies(array $dependencies, DependencyInterface $self): static
    {
        foreach ($self->getDependencyDescriptors() as $descriptor) {
            $property               = $descriptor->getDependencyProperty();
            
            if(isset($this->$property)) {
                continue;
            }
            
            $this->$property = $dependencies[$property] ?? null;
            
            if($this->$property instanceof LazyLoader) {
                $this->$property->setAfterHandler(fn($object) => $this->$property = $object);
            }
        }
        
        return $this;
    }
    
    public function initializeAfterInject(): static
    {
        return $this;
    }
}
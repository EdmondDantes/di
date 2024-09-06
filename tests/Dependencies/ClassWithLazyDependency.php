<?php
declare(strict_types=1);

namespace IfCastle\DI\Dependencies;

use IfCastle\DI\LazyLoader;

class ClassWithLazyDependency
{
    public function __construct(
        public UseConstructorInterface|LazyLoader $some,
    )
    {
        if($this->some instanceof LazyLoader) {
            $this->some->setAfterHandler(fn($object) => $this->some = $object);
        }
    }
}
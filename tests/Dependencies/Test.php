<?php
declare(strict_types=1);

namespace IfCastle\DI\Dependencies;

readonly class Test
{
    public function __construct(
        private UseConstructorInterface $some,
    ) {}
    
    public function test(): void
    {
        $this->some->someMethod();
    }
}
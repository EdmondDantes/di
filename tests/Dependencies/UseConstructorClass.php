<?php
declare(strict_types=1);

namespace IfCastle\DI\Dependencies;

final class UseConstructorClass implements UseConstructorInterface
{
    public static string $data      = '';
    
    public function someMethod(): void
    {
        self::$data                 = 'someMethod';
    }
}
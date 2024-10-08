<?php
declare(strict_types=1);

namespace IfCastle\DI;

interface ContainerMutableInterface extends ContainerInterface
{
    public function set(string $key, mixed $value): static;
    
    public function delete(string $key): static;
}
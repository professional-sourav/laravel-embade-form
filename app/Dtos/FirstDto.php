<?php
namespace App\Dtos;

class FirstDto
{
    private function __construct(public readonly string $name, public readonly int $age) {}
    
    public static function create(string $name, int $age): FirstDto
    {
        return new self($name, $age);
    }
}

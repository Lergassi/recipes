<?php

namespace App\Services;

use Symfony\Component\Validator\Constraint;

class UniqueConstraint extends Constraint
{
    public string $table = '';
    public string $field = '';
    public mixed $value = '';

    public \PDO $pdo;

    public function validatedBy(): string
    {
        return static::class.'Validator';
    }
}
<?php

namespace App\Service\Validation;

use Symfony\Component\Validator\Constraint;

class UniqueConstraint extends Constraint
{
    public string $message = 'Значение {{ value }} уже используется.';

    public string $table = '';
    public string $column = '';
    public ?int $existsID = null;

    public \PDO $pdo;

    public function validatedBy(): string
    {
        return static::class.'Validator';
    }
}
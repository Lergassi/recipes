<?php

namespace App\Service\Validation;

use Symfony\Component\Validator\Constraint;

class ExistsConstraint extends Constraint
{
    public string $message = 'Значение {{ value }} не найдено.';

    public string $table = '';

    public \PDO $pdo;

    public function validatedBy(): string
    {
        return static::class.'Validator';
    }
}
<?php

namespace App\Factories;

use App\Services\Validation\ExistsConstraint;

class ExistsConstraintFactory
{
    private \PDO $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function create(mixed $options = null, array $groups = null, mixed $payload = null): ExistsConstraint
    {
        $constraint = new ExistsConstraint($options, $groups, $payload);
        $constraint->pdo = $this->pdo;

        return $constraint;
    }
}
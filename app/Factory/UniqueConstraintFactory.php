<?php

namespace App\Factory;

use App\Service\Validation\UniqueConstraint;

class UniqueConstraintFactory
{
    private \PDO $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function create(mixed $options = null, array $groups = null, mixed $payload = null): UniqueConstraint
    {
        $constraint = new UniqueConstraint($options, $groups, $payload);
        $constraint->pdo = $this->pdo;

        return $constraint;
    }
}
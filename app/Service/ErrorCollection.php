<?php

namespace App\Service;

use App\Interface\ErrorCollectionInterface;

/**
 * @indev
 */
class ErrorCollection implements ErrorCollectionInterface
{
    private array $errors;

    public function __construct()
    {
        $this->errors = [];
    }

    public function add(string $error): int
    {
        $this->errors[] = $error;

        return count($this->errors);
    }

    public function collect(ErrorCollectionInterface $collect): void
    {
        foreach ($this->errors as $error) {
            $collect->add($error);
        }
    }

    public function count(): int
    {
        return count($this->errors);
    }
}
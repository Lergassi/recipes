<?php

namespace App\Service;

class SerializeReader
{
    private array $data;

    public function __construct()
    {
        $this->data = [];
    }

    public function add(string|int $key, mixed $value): void
    {
        $this->data[$key] = $value;
    }

    public function build(): array
    {
        return $this->data;
    }
}
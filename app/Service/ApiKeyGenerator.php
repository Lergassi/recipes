<?php

namespace App\Service;

use Ramsey\Uuid\Uuid;

class ApiKeyGenerator
{
    public function generate(): string
    {
        return Uuid::uuid4();
    }
}
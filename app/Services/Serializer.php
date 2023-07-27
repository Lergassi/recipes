<?php

namespace App\Services;

class Serializer
{
    public function encode(mixed $data /*todo: options*/): string
    {
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

//    public function decode(): mixed {}
}
<?php

namespace App\Services;

/**
 * @indev
 */
class RequestData
{
    private mixed $data;

    public function __construct(mixed $data)
    {
        $this->data = $data;
    }

    public function getInt(string $key): int
    {
        if (!isset($this->data[$key])) throw new \Exception(sprintf('Параметр %s не найден.', $key));

        return intval($this->data[$key]);
    }
}
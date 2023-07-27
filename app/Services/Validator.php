<?php

namespace App\Services;

class Validator
{
    public function validateRequiredKey(array $array, string|int $key): bool
    {
        return isset($array[$key]);
    }

    /**
     * @param array $array
     * @param (string|int)[] $keys
     * @return bool
     */
    public function validateRequiredKeys(array $array, array $keys): bool
    {
        if (!count($array)) return false;
        if (!count($keys)) return false;

        $result = true;
        foreach ($keys as $key) {
            $result &= $this->validateRequiredKey($array, $key);
        }

        return $result;
    }
}
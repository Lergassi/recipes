<?php

namespace App\Service;

/**
 * @deprecated Использовать symfony/validator.
 */
class RequireValidator
{
    public function validateRequiredKey(array $array, string|int $key, bool $isEmptyValidate = true): bool
    {
        $isSet = isset($array[$key]);

        if ($isEmptyValidate && $isSet) {
            $isSet &= !empty($array[$key]);
        }

        return $isSet;
    }

    /**
     * @param array $array
     * @param (string|int)[] $keys
     * @param bool $isEmptyValidate
     * @return bool
     */
    public function validateRequiredKeys(array $array, array $keys, bool $isEmptyValidate = true): bool
    {
        if (!count($array)) return false;
        if (!count($keys)) return false;

        $result = true;
        foreach ($keys as $key) {
            $result &= $this->validateRequiredKey($array, $key, $isEmptyValidate);
        }

        return $result;
    }
}
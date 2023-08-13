<?php

namespace App\Interface;

/**
 * @indev
 */
interface ErrorCollectionInterface
{
    public function add(string $error): int;
    public function collect(ErrorCollectionInterface $collect): void;
}
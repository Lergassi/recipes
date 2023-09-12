<?php

namespace App\Interface;

use App\Service\SerializeReader;

interface SerializeInterface
{
    /**
     * В каждом объекте определятеся, какие данные доступны для сериализации.
     * @param SerializeReader $reader
     * @return void
     */
    public function serialize(SerializeReader $reader): void;
}
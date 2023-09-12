<?php

namespace App\Interface;

interface RecipeInterface
{
    public function addProduct(int $productID, int $weight): int;
    public function removeProduct(int $productID, int $weight): int;
    //или
    public function addProductBy(object $product, int $weight): bool;
    public function isFree(): bool;
//    public function commit(): void;
}
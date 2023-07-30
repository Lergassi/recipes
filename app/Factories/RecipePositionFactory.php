<?php

namespace App\Factories;

use App\Services\DataManager;

class RecipePositionFactory
{
    private \PDO $pdo;
    private DataManager $dataManager;

    public function __construct(\PDO $pdo, \App\Services\DataManager $dataManager)
    {
        $this->pdo = $pdo;
        $this->dataManager = $dataManager;
    }

//    public function create(int $recipeID): int
//    {
//        $recipe = $this->dataManager->findOneRecipe($recipeID);
//        dd($recipe);
//
//        $query = 'insert into recipe_positions (weight, reference_product_id, recipe_id) VALUES (:weight, :reference_product_id, :recipe_id)';
//        $stmt = $this->pdo->prepare($query);
//
//        $stmt->bindValue(':weight', $recipePosition['weight']);
//        $stmt->bindValue(':reference_product_id', $recipePosition['reference_product_id']);
//        $stmt->bindValue(':recipe_id', $newRecipeId);
//
//        $stmt->execute();
//
//        return 0;
//    }
}
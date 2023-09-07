<?php

namespace App\DataManager;

use DI\Attribute\Inject;

class RecipePositionManager
{
    #[Inject] private \PDO $pdo;

    //todo: Конструктор. Перенести в рецепт. Или отдельный объект обертка для таких запросов.
    public function findOneByProduct(int $recipeID, int $productID): ?array
    {
        $existsRecipePositionQuery = 'select rp.* from recipe_positions rp where rp.recipe_id = :recipe_id and rp.reference_product_id = :reference_product_id';
        $stmt = $this->pdo->prepare($existsRecipePositionQuery);

        $stmt->bindValue(':recipe_id', $recipeID);
        $stmt->bindValue(':reference_product_id', $productID);

        $stmt->execute();

        return $stmt->fetch() ?: null;
    }

    public function findByRecipe(int $recipeID): ?array
    {
        $query = 'select rp.*, r.id as recipe_id, rfp.name as reference_product_name from recipe_positions rp left join recipes r on rp.recipe_id = r.id left join reference_products rfp on rp.reference_product_id = rfp.id where rp.recipe_id = :recipe_id order by rfp.sort';
        $stmt = $this->pdo->prepare($query);

        $stmt->bindValue(':recipe_id', $recipeID);

        $stmt->execute();

        return $stmt->fetchAll();
    }
}
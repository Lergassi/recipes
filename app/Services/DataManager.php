<?php

namespace App\Services;

//todo: Пока 1 файл на все таблицы.
class DataManager
{
    private \PDO $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function count(string $table, string $field, mixed $value): int
    {
        $condition = sprintf('%s = :%s', $field, $field);
        $query = sprintf('select count(*) as count from %s where %s', $table, $condition);

        $stmt = $this->pdo->prepare($query);

        $stmt->bindValue(':' . $field, $value);

        $stmt->execute();

        return $stmt->fetch()['count'];
    }

    public function isMainRecipe(int $ID): bool
    {
        $query = 'select * from recipes where id = :id and is_main = :is_main';
        $stmt = $this->pdo->prepare($query);

        $stmt->bindValue(':id', $ID);
        $stmt->bindValue(':is_main', 1);

        $stmt->execute();

        return $stmt->fetch()['count'] === 1;
    }

    public function findRecipe(int $ID): array|null
    {
        $query = 'select * from recipes where id = :id';
        $stmt = $this->pdo->prepare($query);

        $stmt->bindValue(':id', $ID);

        $stmt->execute();

        $recipe = $stmt->fetch();

        return $recipe ? $recipe : null;
    }

    public function findRecipePosition(int $recipeID, int $productID): array|null
    {
        $existsRecipePositionQuery = 'select rp.* from recipe_positions rp where rp.recipe_id = :recipe_id and rp.reference_product_id = :reference_product_id';
        $stmt = $this->pdo->prepare($existsRecipePositionQuery);

        $stmt->bindValue(':recipe_id', $recipeID);
        $stmt->bindValue(':reference_product_id', $productID);

        $stmt->execute();

        $recipePosition = $stmt->fetch();

        return $recipePosition ? $recipePosition : null;
    }

//    public function findBranch(int $ID)
//    {
//        $query = 'select * from dish_version_branches where id = :id';
//        $stmt = $this->pdo->prepare($query);
//        $stmt->bindValue(':id', $ID);
//        $stmt->execute();
//
//        $branch = $stmt->fetch();
//
//        return $branch ? $branch : null;
//    }
}
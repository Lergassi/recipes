<?php

namespace App\Factories;

use App\Services\DataManager;

class RecipeFactory
{
    private \PDO $pdo;
    private DataManager $dataManager;

    public function __construct(\PDO $pdo, \App\Services\DataManager $dataManager)
    {
        $this->pdo = $pdo;
        $this->dataManager = $dataManager;
    }

    //todo: Можно сделать так: рецепты - это только главные. Копии идут отдельно и не создаются, а копируются из рецептов. Варианты метода copy($recipeID), commit($recipeID).
    public function create(int $branchID, bool $isMain = false): int
    {
        $query = 'insert into recipes (is_main, dish_version_branch_id) VALUES (:is_main, :dish_version_branch_id)';

        $stmt = $this->pdo->prepare($query);

        $stmt->bindValue(':is_main', intval($isMain));
        $stmt->bindValue(':dish_version_branch_id', $branchID);

        $stmt->execute();

        return $this->pdo->lastInsertId();
    }

//    public function copyByRecipe(int $ID): int {return 0;}
//    public function copyByCommit(int $commitID): int {return 0;}
//    public function commit(int $commitID): int {return 0;}
    public function copyByBranch(int $branchID): int
    {
        $branch = $this->dataManager->findOneBranch($branchID); //todo: Тут по идеи должен быть кеш внутри. И когда происходит запрос, например в модуле безопасноти, то если логика доходит до сюда, то повторный запрос не делается.
        if (!$branch) throw new \Exception('Ветка не найдена.');

        $recipe = $this->dataManager->findMainRecipeByBranch($branchID);
        $recipePositions = $this->dataManager->findRecipePositionsByBranch($branchID);
        dump($recipe);
        dump($recipePositions);

//        $insertRecipeQuery = 'insert into recipes (is_main, dish_version_branch_id) VALUES (:is_main, :dish_version_branch_id)';
//        $insertRecipePositionQuery = 'insert into recipe_positions (weight, reference_product_id, recipe_id) VALUES (:weight, :reference_product_id, :recipe_id)';

        return 0;
    }
}
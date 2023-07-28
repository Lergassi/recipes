<?php

namespace App\Factories;

class RecipeFactory
{
    private \PDO $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    //todo: Можно сделать так: рецепты - это только главные. Копии идут отдельно и не создаются, а копируются из рецептов. Варианты метода copy($recipeID), commit($recipeID).
    public function createMain(int $branchID): int
    {
        $query = 'insert into recipes (is_main, dish_version_branch_id) VALUES (:is_main, :dish_version_branch_id)';

        $stmt = $this->pdo->prepare($query);

        $stmt->bindValue(':is_main', 1);
        $stmt->bindValue(':dish_version_branch_id', $branchID);

        $stmt->execute();

        return $this->pdo->lastInsertId();
    }

//    public function copyByRecipe(int $ID): int {return 0;}
//    public function copyByCommit(int $commitID): int {return 0;}
//    public function commit(int $commitID): int {return 0;}
}
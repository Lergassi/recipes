<?php

namespace App\Factories;

class BranchFactory
{
//    private \PDO $pdo;
//    private RecipeFactory $recipeFactory;
//
//    public function __construct(\PDO $pdo, RecipeFactory $recipeFactory)
//    {
//        $this->pdo = $pdo;
//        $this->recipeFactory = $recipeFactory;
//    }
//
//    public function create(int $dishVersionID, string $name, string $description = null): int
//    {
//        $query = 'insert into dish_version_branches (name, description, dish_version_id) VALUES (:name, :description, :dish_version_id)';
//
//        //todo: Только если нет изменений с последнего коммита.
//
//        $stmt = $this->pdo->prepare($query);
//
//        $stmt->bindValue(':name', $name);
//        $stmt->bindValue(':description', $description);
//        $stmt->bindValue(':dish_version_id', $dishVersionID);
//
//        $stmt->execute();
//
//        //todo: Копирование рецепта.
//
//        return $this->pdo->lastInsertId();
//    }
//
//    public function createFromCommit(int $dishVersionID, string $name, string $description = null): int
//    {
//        return 0;
//    }
}
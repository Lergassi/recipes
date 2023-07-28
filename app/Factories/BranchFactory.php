<?php

namespace App\Factories;

class BranchFactory
{
    private \PDO $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function create(int $dishVersionID, string $name, int $commitID = null, string $description = null): int
    {
        $query = 'insert into dish_version_branches (name, description, dish_version_id) VALUES (:name, :description, :dish_version_id)';

        //todo: Только если нет изменений с последнего коммита.

        $stmt = $this->pdo->prepare($query);

        $stmt->bindValue(':name', $name);
        $stmt->bindValue(':description', $description);
        $stmt->bindValue(':dish_version_id', $dishVersionID);

        $stmt->execute();

        //todo: Копирование рецепта.

        return $this->pdo->lastInsertId();
    }
}
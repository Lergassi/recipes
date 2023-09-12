<?php

namespace App\Factory;

use App\Service\DataManager;

/**
 * @deprecated Использовать инструмент(объект) для добавления рецепта прямо в версию.
 */
class RecipeFactory
{
    private \PDO $pdo;
    private DataManager $dataManager;

    public function __construct(\PDO $pdo, DataManager $dataManager)
    {
        $this->pdo = $pdo;
        $this->dataManager = $dataManager;
    }

    /**
     * @deprecated
     * @param string $name
     * @param int $dishVersionID
     * @return int
     */
    public function create(string $name, int $dishVersionID): int
    {
        $query = 'insert into recipes (name, dish_version_id) VALUES (:name, :dish_version_id)';

        $stmt = $this->pdo->prepare($query);

        $stmt->bindValue(':name', $name);
        $stmt->bindValue(':dish_version_id', $dishVersionID);

        $stmt->execute();

        return $this->pdo->lastInsertId();
    }
}
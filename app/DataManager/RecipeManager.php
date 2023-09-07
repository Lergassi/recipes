<?php

namespace App\DataManager;

use DI\Attribute\Inject;

class RecipeManager
{
    #[Inject] private \PDO $pdo;

    public function findOne(int $ID): ?array
    {
        $query = 'select r.* from recipes r where id = :id';
        $stmt = $this->pdo->prepare($query);

        $stmt->bindValue(':id', $ID);

        $stmt->execute();

        return $stmt->fetch() ?: null;
    }

    public function findByDishVersion(int $dishVersionID): array
    {
        $query = 'select r.* from recipes r where r.dish_version_id = :dish_version_id order by r.name';
        $stmt = $this->pdo->prepare($query);

        $stmt->bindValue(':dish_version_id', $dishVersionID);

        $stmt->execute();

        return $stmt->fetchAll();
    }
}
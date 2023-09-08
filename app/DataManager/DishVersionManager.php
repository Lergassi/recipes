<?php

namespace App\DataManager;

use DI\Attribute\Inject;

class DishVersionManager
{
    #[Inject] private \PDO $pdo;

    public function findOne(int $ID): ?array
    {
        $query = 'select dv.* from dish_versions dv where dv.id = :id';
        $stmt = $this->pdo->prepare($query);

        $stmt->bindValue('id', $ID);

        $stmt->execute();

        return $stmt->fetch() ?: null;
    }

    public function findByDish(int $dishID): array
    {
        $query = 'select dv.* from dish_versions dv left join dishes d on dv.dish_id = d.id left join qualities q on d.quality_id = q.id where dish_id = :dish_id order by d.name, dv.name, q.sort';
        $stmt = $this->pdo->prepare($query);

        $stmt->bindValue('dish_id', $dishID);

        $stmt->execute();

        return $stmt->fetchAll();
    }
}
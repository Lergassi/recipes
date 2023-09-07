<?php

namespace App\DataManager;

use DI\Attribute\Inject;

class DishManager
{
    #[Inject] private \PDO $pdo;

    public function findOne(int $ID): ?array
    {
        $query = 'select d.* from dishes d where d.id = :id';
        $stmt = $this->pdo->prepare($query);

        $stmt->bindValue(':id', $ID);

        $stmt->execute();

        return $stmt->fetch() ?: null;
    }

    //todo: Возможно сортировку можно указывать отдельно.
    public function find(): array
    {
        $query = 'select d.* from dishes d left join qualities q on d.quality_id = q.id order by d.name, q.sort';
        $stmt = $this->pdo->prepare($query);

        $stmt->execute();

        return $stmt->fetchAll();
    }
}
<?php

namespace App\DataManager;

use App\Entity\User;
use DI\Attribute\Inject;

class DishVersionManager
{
    #[Inject] private \PDO $pdo;

    public function findOne(int $ID, User $user = null): ?array
    {
        $query =
            'select
                dv.*
            from dish_versions dv
            where dv.id = :id';
        if ($user) $query .= ' and author_id = :author_id';
        $stmt = $this->pdo->prepare($query);

        $stmt->bindValue('id', $ID);
        if ($user) $stmt->bindValue(':author_id', $user->getID());

        $stmt->execute();

        return $stmt->fetch() ?: null;
    }

    public function findByDish(int $dishID, User $user = null): array
    {
        $queryPattern = 'select dv.* from dish_versions dv left join dishes d on dv.dish_id = d.id left join qualities q on d.quality_id = q.id where dish_id = :dish_id %s order by d.name, dv.name, q.sort';
        $query = sprintf($queryPattern, $user ? 'and dv.author_id = :author_id' : '');
        $stmt = $this->pdo->prepare($query);

        $stmt->bindValue('dish_id', $dishID);
        if ($user) $stmt->bindValue(':author_id', $user->getID());

        $stmt->execute();

        return $stmt->fetchAll();
    }
}
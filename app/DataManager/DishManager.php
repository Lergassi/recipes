<?php

namespace App\DataManager;

use App\Entity\User;
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

    public function findOneByUser(int $ID, User $user): ?array
    {
        $query = 'select d.* from dishes d where d.id = :id and author_id = :author_id';
        $stmt = $this->pdo->prepare($query);

        $stmt->bindValue(':id', $ID);
        $stmt->bindValue(':author_id', $user->getID());

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

    public function findByUser(User $user): array
    {
        $query =
            'select
                d.*
            from dishes d
                left join qualities q on d.quality_id = q.id
            where author_id = :author_id
            order by d.name, q.sort';
        $stmt = $this->pdo->prepare($query);

        $stmt->bindValue(':author_id', $user->getID());

        $stmt->execute();

        return $stmt->fetchAll();
    }
}
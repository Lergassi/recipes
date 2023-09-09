<?php

namespace App\DataManager;

use App\Entity\User;
use DI\Attribute\Inject;

class RecipeManager
{
    #[Inject] private \PDO $pdo;

    public function findOne(int $ID, User $user = null): ?array
    {
        $query = 'select r.* from recipes r where id = :id';
        if ($user) $query .= ' and r.author_id = :author_id';
        $stmt = $this->pdo->prepare($query);

        $stmt->bindValue(':id', $ID);
        if ($user) $stmt->bindValue(':author_id', $user->getID());

        $stmt->execute();

        return $stmt->fetch() ?: null;
    }

    public function findByDishVersion(int $dishVersionID, User $user = null): array
    {
        $queryPattern = 'select r.* from recipes r where r.dish_version_id = :dish_version_id %s order by r.name';
        $query = sprintf($queryPattern, $user ? 'and author_id = :author_id' : '');
        $stmt = $this->pdo->prepare($query);

        $stmt->bindValue(':dish_version_id', $dishVersionID);
        if ($user) $stmt->bindValue(':author_id', $user->getID());

        $stmt->execute();

        return $stmt->fetchAll();
    }
}
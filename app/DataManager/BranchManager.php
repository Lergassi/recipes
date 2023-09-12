<?php

namespace App\DataManager;

use DI\Attribute\Inject;

/**
 * @deprecated Branch сейчас нету.
 */
class BranchManager
{
    #[Inject] private \PDO $pdo;

    public function findOne(int $ID): ?array
    {
        $query = 'select * from dish_version_branches dvb where dvb.id = :id';
        $stmt = $this->pdo->prepare($query);

        $stmt->bindValue(':id', $ID);

        $stmt->execute();

        return $stmt->fetch() ?: null;
    }

    public function find(int $dishVersionID): array
    {
        $branchesQuery = 'select * from dish_version_branches dvb where dvb.dish_version_id = :dish_version_id';
        $branchesQueryStmt = $this->pdo->prepare($branchesQuery);

        $branchesQueryStmt->bindValue(':dish_version_id', $dishVersionID);

        $branchesQueryStmt->execute();

        return $branchesQueryStmt->fetchAll();
    }
}
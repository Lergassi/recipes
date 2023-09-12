<?php

namespace App\DataManager;

use DI\Attribute\Inject;

class ReferenceProductManager
{
    #[Inject] private \PDO $pdo;

    public function findOne(int $ID): ?array
    {
        $query = 'select rp.* from reference_products rp where rp.id = :id';
        $stmt = $this->pdo->prepare($query);

        $stmt->bindValue(':id', $ID);

        $stmt->execute();

        return $stmt->fetch() ?: null;
    }

    public function findOneByAlias(mixed $alias)
    {
        $query = 'select rp.* from reference_products rp where rp.alias = :alias';
        $stmt = $this->pdo->prepare($query);

        $stmt->bindValue(':alias', $alias);

        $stmt->execute();

        return $stmt->fetch() ?: null;
    }

    public function find(): array
    {
        $query = 'select rp.* from reference_products rp order by rp.name';
        $stmt = $this->pdo->prepare($query);

        $stmt->execute();

        $fetch = $stmt->fetchAll();

        return $fetch;
    }
}
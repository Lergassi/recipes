<?php

namespace App\DataManager;

use DI\Attribute\Inject;

class QualityManager
{
    #[Inject] private \PDO $pdo;

    public function findOne(int $ID): ?array
    {
        $query = 'select * from qualities where id = :id';
        $stmt = $this->pdo->prepare($query);

        $stmt->bindValue(':id', $ID);

        $stmt->execute();

        return $stmt->fetch() ?: null;
    }

    //todo: Отдельный инструмент для алиасов.
    public function findOneByAlias(string $alias)
    {
        $query = 'select * from qualities where alias = :alias';
        $stmt = $this->pdo->prepare($query);

        $stmt->bindValue('alias', $alias);

        $stmt->execute();

        return $stmt->fetch() ?: null;
    }

    public function find(): array
    {
        $query = 'select * from qualities order by sort';
        $stmt = $this->pdo->prepare($query);

        $stmt->execute();

        return $stmt->fetchAll();
    }
}
<?php

namespace App\Service;

use DI\Attribute\Inject;

/**
 * @deprecated
 */
class DataManager
{
    #[Inject] private \PDO $pdo;

    /**
     * @deprecated Использовать "точечные" методы.
     * @param string $table
     * @param string $field
     * @param mixed $value
     * @return int
     */
    public function count(string $table, string $field, mixed $value): int
    {
        $condition = sprintf('%s = :%s', $field, $field);
        $query = sprintf('select count(*) as count from %s where %s', $table, $condition);

        $stmt = $this->pdo->prepare($query);

        $stmt->bindValue(':' . $field, $value);

        $stmt->execute();

        return $stmt->fetch()['count'];
    }
}
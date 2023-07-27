<?php

namespace App\Services;

//todo: Пока 1 файл на все таблицы.
class DataManager
{
    private \PDO $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

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
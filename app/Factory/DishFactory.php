<?php

namespace App\Factory;

use App\Entity\User;
use DI\Attribute\Inject;
use Respect\Validation\Validator;

class DishFactory
{
    #[Inject] private \PDO $pdo;

    public function create(
        string $name,
        string $alias,
        array $quality,
        User $user,
    ): array
    {
        Validator::notBlank()->length(null, 128)->assert($name);
        Validator::notBlank()->length(null, 150)->assert($alias);   //todo: @demo_disable unique для алиасов отключено для демо. Нужно найти решение для алиасов для бд и кода при наличии пользователей. Или вообще не делать.

        $dish = [
            'name' => $name,
            'alias' => $alias,
            'quality_id' => $quality['id'],
            'author_id' => $user->getID(),
        ];

        $query = 'insert into dishes (name, alias, quality_id, author_id) values (:name, :alias, :quality_id, :author_id)';

        $stmt = $this->pdo->prepare($query);

        $stmt->bindValue(':name', $dish['name']);
        $stmt->bindValue(':alias', $dish['alias']);
        $stmt->bindValue(':quality_id', $dish['quality_id']);
        $stmt->bindValue(':author_id', $dish['author_id']);

        $stmt->execute();

        $dish['id'] = $this->pdo->lastInsertId();

        return $dish;
    }
}
<?php

namespace App\DataService;

use DI\Attribute\Inject;
use Respect\Validation\Validator;

class DishVersionService
{
    #[Inject] private \PDO $pdo;

    /**
     * @param array $dishVersion Предположим, что тут объект.
     * @param string $name
     * @return array
     */
    public function addRecipe(array $dishVersion, string $name): array
    {
        Validator::notBlank()->length(null, 256)->assert($name);

        $query = 'insert into recipes (name, dish_version_id, author_id) VALUES (:name, :dish_version_id, :author_id)';

        $stmt = $this->pdo->prepare($query);

        $stmt->bindValue(':name', $name);
        $stmt->bindValue(':dish_version_id', $dishVersion['id']);
        $stmt->bindValue(':author_id', $dishVersion['author_id']);

        $stmt->execute();

        return [
            'id' => $this->pdo->lastInsertId(),
            'name' => $name,
            'dish_version_id' => $dishVersion['id'],
            'author_id' => $dishVersion['author_id'],
        ];
    }

//    public function removeRecipe(int $recipeID): bool
//    {
//        return false;
//    }
//
//    public function update(array $dishVersion, string $name, string $alias, int $qualityID): bool
//    {
//        //todo: validate
//        $dishVersion['name'] = $name;
//        $dishVersion['alias'] = $alias;
//        $dishVersion['quality_id'] = $qualityID;
//
//        $query = 'update dish_versions set name = :name, alias = :alias, quality_id = :quality_id where id = :id';
//
//        $stmt = $this->pdo->prepare($query);
//
//        $stmt->bindValue(':id', $dishVersion['id']);
//        $stmt->bindValue(':name', $dishVersion['name']);
//        $stmt->bindValue(':alias', $dishVersion['alias']);
//        $stmt->bindValue(':quality_id', $dishVersion['quality_id']);
//
//        $stmt->execute();
//
//        return boolval($stmt->rowCount());
//    }
}
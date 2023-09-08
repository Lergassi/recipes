<?php

namespace App\DataService;

use DI\Attribute\Inject;
use Respect\Validation\Validator;

class DishService
{
    #[Inject] private \PDO $pdo;

    public function addDishVersion(array $dish, string $name, string $alias, array $quality): array
    {
        //todo: Если нужна обратная связь для клиента - будет отдельное решение.
        Validator::notBlank()->length(null, 128)->assert($name);
        Validator::notBlank()->length(null, 150)->assert($alias);

        $query = 'insert into dish_versions (name, alias, dish_id, quality_id) values (:name, :alias, :dish_id, :quality_id)';

        $stmt = $this->pdo->prepare($query);

        $stmt->bindValue(':name', $name);
        $stmt->bindValue(':alias', $alias);
        $stmt->bindValue(':dish_id', $dish['id']);
        $stmt->bindValue(':quality_id', $quality['id']);

        $stmt->execute();

        return [
            'id' => $this->pdo->lastInsertId(),
            'name' => $name,
            'alias' => $alias,
            'dish_id' => $dish['id'],
        ];
    }

//    public function removeDishVersion(int $dishVersionID): void
//    {
//        //@sql_test Что лучше: сделать запрос с условием или сначала получить через select dish_version?
////        $query = 'delete from dish_versions where id = :id and dish_id = :dish_id and user_id = :user_id';
//        //Таким образом пропадает необходимость делать доп запрос. Но становиться не удобным отслеживание работы программы. Или делать несколько запросов для формирование одного объекта?
//        $query = 'delete from dish_versions where id = :id and dish_id = :dish_id';
//        $stmt = $this->pdo->prepare($query);
//
//        $stmt->bindValue(':id', $dishVersionID);
//        $stmt->bindValue(':dish_id', $this->dish['id']);
////        $stmt->bindValue(':user_id', $this->dish['user_id']);
//
//        $stmt->execute();
//        //Обновление данных на клиенте.
//    }
}
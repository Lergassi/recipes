<?php

namespace App\DataManager;

use DI\Attribute\Inject;

class DishManager
{
    #[Inject] private \PDO $pdo;
    #[Inject] private QualityManager $qualityManager;

    public function findOne(int $ID): ?array
    {
        $query = 'select d.* from dishes d where d.id = :id';
        $stmt = $this->pdo->prepare($query);

        $stmt->bindValue(':id', $ID);

        $stmt->execute();

        $item = $stmt->fetch();
        if (!$item) return null;

        return $this->build($item);
    }

    //todo: Возможно сортировку можно указывать отдельно.
    public function find(): array
    {
        $query = 'select d.* from dishes d left join qualities q on d.quality_id = q.id order by d.name, q.sort';
        $stmt = $this->pdo->prepare($query);

        $stmt->execute();

        $items = $stmt->fetchAll();
        foreach ($items as &$item) {
            $item = $this->build($item);
        }

        return $items;
    }

    private function build(array $data): array
    {
        //todo: Возможно это нужно убрать на уровень формирования ответа для api. Внутри программы всё равно нет единой логики. Особенно в коммитах и RecipePosition. Но пока тут.
        $data['quality'] = $this->qualityManager->findOne($data['quality_id']);
        unset($data['quality_id']);

        return $data;
    }
}
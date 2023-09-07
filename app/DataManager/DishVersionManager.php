<?php

namespace App\DataManager;

use DI\Attribute\Inject;

class DishVersionManager
{
    #[Inject] private \PDO $pdo;
    #[Inject] private QualityManager $qualityManager;

    public function findOne(int $ID): ?array
    {
        $query = 'select dv.* from dish_versions dv where dv.id = :id';
        $stmt = $this->pdo->prepare($query);

        $stmt->bindValue('id', $ID);

        $stmt->execute();

        $item = $stmt->fetch();
        if (!$item) return null;

        return $this->build($item);
    }

    public function findByDish(int $dishID): array
    {
        $query = 'select dv.* from dish_versions dv left join dishes d on dv.dish_id = d.id left join qualities q on d.quality_id = q.id where dish_id = :dish_id order by d.name, dv.name, q.sort';
        $stmt = $this->pdo->prepare($query);

        $stmt->bindValue('dish_id', $dishID);

        $stmt->execute();

        $items = $stmt->fetchAll();
        foreach ($items as &$item) {
            $item = $this->build($item);
        }

        return $items;
    }

    private function build(array $data): array
    {
        $data['quality'] = $this->qualityManager->findOne($data['quality_id']);
        unset($data['quality_id']);

        return $data;
    }
}
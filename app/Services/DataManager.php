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

    public function isMainRecipe(int $ID): bool
    {
        $query = 'select * from recipes where id = :id and is_main = :is_main';
        $stmt = $this->pdo->prepare($query);

        $stmt->bindValue(':id', $ID);
        $stmt->bindValue(':is_main', 1);

        $stmt->execute();

        return $stmt->fetch()['count'] === 1;
    }

    public function findOneRecipe(int $ID): array|null
    {
        $query = 'select * from recipes where id = :id';
        $stmt = $this->pdo->prepare($query);

        $stmt->bindValue(':id', $ID);

        $stmt->execute();

        return $this->returnOneOrNull($stmt->fetch());
    }

    public function findMainRecipeByBranch(int $branchID): array|null
    {
        $query = 'select r.* from recipes r where r.dish_version_branch_id = :dish_version_branch_id and r.is_main = :is_main';
        $stmt = $this->pdo->prepare($query);

        $stmt->bindValue(':dish_version_branch_id', $branchID);
        $stmt->bindValue(':is_main', 1);

        $stmt->execute();

        return $this->returnOneOrNull($stmt->fetch());
    }

    public function findRecipePositionByProduct(int $recipeID, int $productID): array|null
    {
        $existsRecipePositionQuery = 'select rp.* from recipe_positions rp where rp.recipe_id = :recipe_id and rp.reference_product_id = :reference_product_id';
        $stmt = $this->pdo->prepare($existsRecipePositionQuery);

        $stmt->bindValue(':recipe_id', $recipeID);
        $stmt->bindValue(':reference_product_id', $productID);

        $stmt->execute();

        return $this->returnOneOrNull($stmt->fetch());
    }

    public function findOneBranch(int $ID)
    {
        $query = 'select * from dish_version_branches dvb where dvb.id = :id';
        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(':id', $ID);
        $stmt->execute();
        $branch = $stmt->fetch();    //todo: Если не запустить execute будет false/[]. Так задумано?

        return $this->returnOneOrNull($branch);
    }

    /**
     * @param int $ID
     * @return array|false
     */
    public function findRecipePositionsByBranch(int $ID)
    {
        $query = 'select rp.* from recipe_positions rp left join recipes r on rp.recipe_id = r.id left join dish_version_branches dvb on r.dish_version_branch_id = dvb.id left join reference_products rfp on rp.reference_product_id = rfp.id where dvb.id = :id order by rfp.sort';
        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(':id', $ID);
//        $stmt->bindValue(':is_main', 1);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function findOneDishVersion(int $ID): array|null
    {
        $selectDishVersionQuery = 'select dv.* from dish_versions dv where dv.id = :id';

        $selectDishVersionStmt = $this->pdo->prepare($selectDishVersionQuery);
        $selectDishVersionStmt->bindValue('id', $ID);
        $selectDishVersionStmt->execute();

        $dishVersion = $selectDishVersionStmt->fetch();

        return $this->returnOneOrNull($dishVersion);
    }

    public function findBranches(int $ID)
    {
        $branchesQuery = 'select * from dish_version_branches dvb where dvb.dish_version_id = :dish_version_id';
        $branchesQueryStmt = $this->pdo->prepare($branchesQuery);
        $branchesQueryStmt->bindValue(':dish_version_id', $ID);
        $branchesQueryStmt->execute();

        return $branchesQueryStmt->fetchAll();
    }

    private function returnOneOrNull(mixed $target): mixed
    {
        return $target ?: null;
    }
}
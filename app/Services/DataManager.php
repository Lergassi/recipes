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
     * @param int $recipeID
     * @return array|false
     */
    public function findRecipePositions(int $recipeID)
    {
        $query = 'select rp.*, r.id as recipe_id from recipe_positions rp left join recipes r on rp.recipe_id = r.id left join reference_products rfp on rp.reference_product_id = rfp.id where rp.recipe_id = :recipe_id order by rfp.sort';
        $stmt = $this->pdo->prepare($query);

        $stmt->bindValue(':recipe_id', $recipeID);

        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function findOneDishVersion(int $ID): array|null
    {
        $query = 'select dv.* from dish_versions dv where dv.id = :id';

        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue('id', $ID);
        $stmt->execute();

        return $this->returnOneOrNull($stmt->fetch());
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

    public function findOneQualityByAlias(string $alias)
    {
        $query = 'select * from qualities where alias = :alias';

        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue('alias', $alias);
        $stmt->execute();

        return $this->returnOneOrNull($stmt->fetch());
    }

    public function findRecipes(int $dishVersionID)
    {
        $query = 'select r.* from recipes r where r.dish_version_id = :dish_version_id';

        $stmt = $this->pdo->prepare($query);

        $stmt->bindValue(':dish_version_id', $dishVersionID);

        $stmt->execute();

        return $stmt->fetchAll();
    }

    //todo: Наверное можно оставить только head. Если head нету - значит рецепт новый. Иначе head всегда указывает на последний коммит. И коммит не предыдущий а текущий.
    public function findPreviousRecipeCommit(int $recipeID): array|null
    {
        $query = 'select rc.* from recipe_commits rc left join heads h on rc.id = h.recipe_commit_id where rc.recipe_id = :recipe_id and rc.id = h.recipe_commit_id';
        $stmt = $this->pdo->prepare($query);

        $stmt->bindValue('recipe_id', $recipeID);

        $stmt->execute();

        return $this->returnOneOrNull($stmt->fetch());
    }

    public function findHeadRecipeCommit(int $recipeID): array|null
    {
        return $this->findPreviousRecipeCommit($recipeID);
    }

    public function findOneDish(int $ID): array|null
    {
        $query = 'select d.* from dishes d where d.id = :id';
        $stmt = $this->pdo->prepare($query);

        $stmt->bindValue(':id', $ID);

        $stmt->execute();

        return $this->returnOneOrNull($stmt->fetch());
    }

    public function findDishVersions(int $dishID): array
    {
        $query = 'select dv.*, d.id as dish_id from dish_versions dv left join dishes d on dv.dish_id = d.id left join qualities q on d.quality_id = q.id where dish_id = :dish_id order by d.name, dv.name, q.sort';
        $stmt = $this->pdo->prepare($query);

        $stmt->bindValue('dish_id', $dishID);

        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function findDishes(): array
    {
        $query = 'select d.* from dishes d left join qualities q on d.quality_id = q.id order by d.name, q.sort';
        $stmt = $this->pdo->prepare($query);

        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function findQualities(): array
    {
        $query = 'select * from qualities order by sort';
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function findReferenceProducts(): array
    {
        $query = 'select * from reference_products order by name';
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
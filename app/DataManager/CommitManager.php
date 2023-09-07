<?php

namespace App\DataManager;

use DI\Attribute\Inject;

class CommitManager
{
    #[Inject] private \PDO $pdo;

    //todo: Наверное можно оставить только head. Если head нету - значит рецепт новый. Иначе head всегда указывает на последний коммит.
    public function findOnePreviousRecipeCommit(int $recipeID): ?array
    {
        $query = 'select rc.* from recipe_commits rc right join heads h on rc.id = h.recipe_commit_id where h.recipe_id = :recipe_id';
        $stmt = $this->pdo->prepare($query);

        $stmt->bindValue('recipe_id', $recipeID);

        $stmt->execute();

        return $stmt->fetch() ?: null;
    }

    public function findHeadRecipeCommit(int $recipeID): ?array
    {
        return $this->findOnePreviousRecipeCommit($recipeID);
    }

    public function findCommitRecipePositions(int $recipeCommitID): array
    {
        $query = 'select * from recipe_commit_positions where recipe_commit_id = :recipe_commit_id';
        $stmt = $this->pdo->prepare($query);

        $stmt->bindValue(':recipe_commit_id', $recipeCommitID);

        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function diffWithCurrentRecipe(int $recipeID): array
    {
        //todo: Название запросов.
        $query =
            'select
                rp.reference_product_id
                ,rfp.name
                ,rcp.weight     # было, если null то продукт новый
                ,rp.weight      # стало
            from recipe_positions rp
                left join reference_products rfp on rp.reference_product_id = rfp.id
                left join recipes r on rp.recipe_id = r.id
                left join heads h on r.id = h.recipe_id
                left join recipe_commits rc on h.recipe_commit_id = rc.id
                left join recipe_commit_positions rcp on rc.id = rcp.recipe_commit_id and rp.reference_product_id = rcp.reference_product_id
            where
                r.id = :recipe_id
                and
                (
                    rp.weight <> rcp.weight
                    or rcp.weight is null
                )
            union
            select
                rcp.reference_product_id
                ,rfp.name
                ,rcp.weight     # было
                ,rp.weight      # стало, если null то продукт удален
            
            from recipe_commit_positions rcp
                left join reference_products rfp on rcp.reference_product_id = rfp.id
                left join heads h on rcp.recipe_commit_id = h.recipe_commit_id
                left join recipe_commits rc on rcp.recipe_commit_id = rc.id and rc.id = h.recipe_commit_id # !!!
                left join recipes r on rc.recipe_id = r.id
                left join recipe_positions rp on r.id = rp.recipe_id and rcp.reference_product_id = rp.reference_product_id
            where
                r.id = :recipe_id
                and rp.weight is null'
        ;

        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':recipe_id', $recipeID);

        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function hasDiffWithCurrentRecipe(int $recipeID): bool
    {
        $query =
            'select count(*) as count
            from (
                select
                    rp.reference_product_id
                    ,rfp.name
                    ,rcp.weight     # было, если null то продукт новый
                    ,rp.weight as rp_weight      # стало
                from recipe_positions rp
                    left join reference_products rfp on rp.reference_product_id = rfp.id
                    left join recipes r on rp.recipe_id = r.id
                    left join heads h on r.id = h.recipe_id
                    left join recipe_commits rc on h.recipe_commit_id = rc.id
                    left join recipe_commit_positions rcp on rc.id = rcp.recipe_commit_id and rp.reference_product_id = rcp.reference_product_id
                where
                    r.id = :recipe_id
                    and
                    (
                        rp.weight <> rcp.weight
                        or rcp.weight is null
                    )
                union
                select
                    rcp.reference_product_id
                    ,rfp.name
                    ,rcp.weight     # было
                    ,rp.weight      # стало, если null то продукт удален
                from recipe_commit_positions rcp
                    left join reference_products rfp on rcp.reference_product_id = rfp.id
                    left join heads h on rcp.recipe_commit_id = h.recipe_commit_id
                    left join recipe_commits rc on rcp.recipe_commit_id = rc.id and rc.id = h.recipe_commit_id # !!!
                    left join recipes r on rc.recipe_id = r.id
                    left join recipe_positions rp on r.id = rp.recipe_id and rcp.reference_product_id = rp.reference_product_id
                where
                    r.id = :recipe_id
                    and rp.weight is null
                 ) as sum_table'
        ;

        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':recipe_id', $recipeID);

        $stmt->execute();

        $fetch = $stmt->fetch();

        return $fetch['count'] !== 0;
    }
}
<?php

namespace App\Service;

use App\DataManager\CommitManager;
use App\DataManager\RecipePositionManager;
use App\Factory\RecipeFactory;
use DI\Attribute\Inject;

class RecipeService
{
    #[Inject] private DataManager $dataManager;
    #[Inject] private RecipePositionManager $recipePositionManager;
    #[Inject] private CommitManager $commitManager;
    #[Inject] private \PDO $pdo;
    #[Inject] private RecipeFactory $recipeFactory;

    public function addProduct(int $recipeID, int $referenceProductID, int $weight): int {
        if ($weight <= 0) return 0;

        //todo: Возможно можно оптимизировать и запросить сразу все позиции.
        $recipePosition = $this->recipePositionManager->findOneByProduct($recipeID, $referenceProductID);
        if (!$recipePosition) {
            $insertRecipePositionQuery = 'insert into recipe_positions (weight, recipe_id, reference_product_id) VALUES (:weight, :recipe_id, :reference_product_id)';
            $insertRecipePositionStmt = $this->pdo->prepare($insertRecipePositionQuery);

            $insertRecipePositionStmt->bindValue(':weight', $weight);   //todo: validate
            $insertRecipePositionStmt->bindValue(':recipe_id', $recipeID);
            $insertRecipePositionStmt->bindValue(':reference_product_id', $referenceProductID);   //todo: В класс: не понятна ошибка. И рецепты тоже.

            $insertRecipePositionStmt->execute();
        } else {
            $recipePosition['weight'] += $weight;                             //todo: validate

            $updateRecipePositionQuery = 'update recipe_positions set weight = :weight where id = :id';
            $updateRecipePositionStmt = $this->pdo->prepare($updateRecipePositionQuery);

            $updateRecipePositionStmt->bindValue(':weight', $recipePosition['weight']);
            $updateRecipePositionStmt->bindValue(':id', $recipePosition['id']);

            $updateRecipePositionStmt->execute();
        }

        return $weight;
    }

    public function removeProduct(int $recipeID, int $referenceProductID, int $weight): int
    {
        if ($weight <= 0) return 0;

        $recipePosition = $this->recipePositionManager->findOneByProduct($recipeID, $referenceProductID);
        $removedWeight = 0;
        if ($recipePosition) {
            if ($recipePosition['weight'] >= $weight) {
                $recipePosition['weight'] -= $weight;
                $removedWeight = $weight;
            } else {
                $removedWeight = $recipePosition['weight'];
                $recipePosition['weight'] = 0 ;
            }

            if ($recipePosition['weight'] > 0) {
                $updateRecipePositionQuery = 'update recipe_positions set weight = :weight where id = :id';
                $updateRecipePositionStmt = $this->pdo->prepare($updateRecipePositionQuery);

                $updateRecipePositionStmt->bindValue(':weight', $recipePosition['weight']);
                $updateRecipePositionStmt->bindValue(':id', $recipePosition['id']);

                $updateRecipePositionStmt->execute();
            } else {
                $deleteRecipePositionQuery = 'delete from recipe_positions where id = :id';
                $deleteRecipePositionStmt = $this->pdo->prepare($deleteRecipePositionQuery);

                $deleteRecipePositionStmt->bindValue(':id', $recipePosition['id']);

                $deleteRecipePositionStmt->execute();
            }
        }
        
        return $removedWeight;
    }

    //todo: Вариант для поиска решения проверок и возвращения ошибок.
//    public function branch(int $recipeID, string $name): int
//    {
//        $recipe = $this->dataManager->findOneRecipe($recipeID);
//        if (!$recipe) throw new \Exception('Рецепт не найден.');
//
//        $head = $this->dataManager->findHeadRecipeCommit($recipeID);
//        if (!$head) throw new \Exception('Нельзя скопировать рецепт без коммита.');
//
//        if ($this->dataManager->hasDiffWithCurrentRecipe($recipeID)) throw new \Exception('Нельзя создать рецепт. Текущий рецепт имеет незафиксированные изменения.');
//
//        $newRecipeID = $this->recipeFactory->create($name, $recipe['dish_version_id']);
//
//        $recipePositions = $this->dataManager->findRecipePositions($recipeID);
//        foreach ($recipePositions as $recipePosition) {
//            $this->addProduct($newRecipeID, $recipePosition['reference_product_id'], $recipePosition['weight']);
//        }
//
//        $this->updateHead($newRecipeID, $head['id']);
//
//        return $newRecipeID;
//    }

//    public function copy(int $recipeID, string $name): int
//    {
//        $recipe = $this->dataManager->findOneRecipe($recipeID);
//        $newRecipeID = $this->recipeFactory->create($name, $recipe['dish_version_id']);
//
//        $recipePositions = $this->dataManager->findRecipePositions($recipe['id']);
//        foreach ($recipePositions as $recipePosition) {
//            $this->addProduct($newRecipeID, $recipePosition['reference_product_id'], $recipePosition['weight']);
//        }
//
//        $this->commit($newRecipeID);
//    }

    public function createRecipeCommitPosition(): int
    {
        $insertRecipeCommitPositionQuery = 'insert into recipe_commit_positions (weight, reference_product_id, recipe_commit_id) VALUES (:weight, :reference_product_id, :recipe_commit_id)';
        $insertRecipeCommitPositionStmt = $this->pdo->prepare($insertRecipeCommitPositionQuery);

        return 0;
    }

    public function commit(int $recipeID, int $previousRecipeCommitID = null)
    {
        $insertRecipeCommitQuery = 'insert into recipe_commits (recipe_id, previous_commit_id) VALUES (:recipe_id, :previous_commit_id)';
        $insertRecipeCommitStmt = $this->pdo->prepare($insertRecipeCommitQuery);

        $insertRecipeCommitStmt->bindValue(':recipe_id', $recipeID);
        $insertRecipeCommitStmt->bindValue(':previous_commit_id', $previousRecipeCommitID);

        $insertRecipeCommitStmt->execute();
        $recipeCommitID = $this->pdo->lastInsertId();

        $insertRecipeCommitPositionQuery = 'insert into recipe_commit_positions (weight, reference_product_id, recipe_commit_id) VALUES (:weight, :reference_product_id, :recipe_commit_id)';
        $insertRecipeCommitPositionStmt = $this->pdo->prepare($insertRecipeCommitPositionQuery);

        $recipePositions = $this->recipePositionManager->findByRecipe($recipeID);
        foreach ($recipePositions as $recipePosition) {
            $insertRecipeCommitPositionStmt->bindValue(':weight', $recipePosition['weight']);
            $insertRecipeCommitPositionStmt->bindValue(':reference_product_id', $recipePosition['reference_product_id']);
            $insertRecipeCommitPositionStmt->bindValue(':recipe_commit_id', $recipeCommitID);

            $insertRecipeCommitPositionStmt->execute();
        }

        $this->updateHead($recipeID, $recipeCommitID);

        return $recipeCommitID;
    }

    private function updateHead(int $recipeID, int $recipeCommitID): void
    {
        $head = $this->commitManager->findHeadRecipeCommit($recipeID);
        if (!$head) {
            $insertHeadQuery = 'insert into heads (recipe_id, recipe_commit_id) values (:recipe_id, :recipe_commit_id)';
            $insertHeadStmt = $this->pdo->prepare($insertHeadQuery);

            $insertHeadStmt->bindValue('recipe_id', $recipeID);
            $insertHeadStmt->bindValue('recipe_commit_id', $recipeCommitID);

            $insertHeadStmt->execute();
        } else {
            $updateHeadQuery = 'update heads set recipe_commit_id = :recipe_commit_id where recipe_id = :recipe_id';
            $updateHeadStmt = $this->pdo->prepare($updateHeadQuery);

            $updateHeadStmt->bindValue('recipe_id', $recipeID);
            $updateHeadStmt->bindValue('recipe_commit_id', $recipeCommitID);

            $updateHeadStmt->execute();
        }
    }
}
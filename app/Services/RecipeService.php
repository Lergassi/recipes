<?php

namespace App\Services;

use App\Factories\RecipeFactory;

class RecipeService
{
    private DataManager $dataManager;
    private \PDO $pdo;
    private RecipeFactory $recipeFactory;

    public function __construct(DataManager $dataManager, \PDO $pdo, \App\Factories\RecipeFactory $recipeFactory)
    {
        $this->dataManager = $dataManager;
        $this->pdo = $pdo;
        $this->recipeFactory = $recipeFactory;
    }

    public function addProduct(int $recipeID, int $referenceProductID, int $weight): int {
        $recipePosition = $this->dataManager->findRecipePositionByProduct($recipeID, $referenceProductID);
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
        $recipePosition = $this->dataManager->findRecipePositionByProduct($recipeID, $referenceProductID);
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

    public function copy(int $recipeID, string $name): int
    {
        $recipe = $this->dataManager->findOneRecipe($recipeID);
        if (!$recipe) throw new \Exception('Рецепт не найден.');

        $head = $this->dataManager->findHeadRecipeCommit($recipeID);
        if (!$head) throw new \Exception('Нельзя скопировать рецепт без коммита.');

        //todo: Проверка на изменения с последнего коммита. С изменениями нельзя скопировать рецепт.

        $newRecipeID = $this->recipeFactory->create($name, $recipe['dish_version_id']);

        $recipePositions = $this->dataManager->findRecipePositions($recipeID);
        foreach ($recipePositions as $recipePosition) {
            $this->addProduct($newRecipeID, $recipePosition['reference_product_id'], $recipePosition['weight']);
        }

        $this->updateHead($newRecipeID, $head['id']);

        return $newRecipeID;
    }

    public function updateHead(int $recipeID, int $recipeCommitID): int
    {
        $head = $this->dataManager->findHeadRecipeCommit($recipeID);
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

        return $recipeCommitID;
    }
}
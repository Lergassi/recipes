<?php

namespace App\Controllers;

use App\Factories\BranchFactory;
use App\Factories\RecipeFactory;
use App\Services\DataManager;
use App\Services\ResponseBuilder;
use App\Services\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class RecipeController
{
    private \PDO $pdo;
    private BranchFactory $branchFactory;
    private RecipeFactory $recipeFactory;
    private Validator $validator;
    private ResponseBuilder $responseBuilder;
    private DataManager $dataManager;

    public function __construct(
        \PDO            $pdo,
        ResponseBuilder $responseBuilder,
        Validator       $validator,
        DataManager     $dataManager,
        BranchFactory   $branchFactory,
        RecipeFactory   $recipeFactory,
    )
    {
        $this->pdo = $pdo;
        $this->responseBuilder = $responseBuilder;
        $this->validator = $validator;
        $this->dataManager = $dataManager;
        $this->branchFactory = $branchFactory;
        $this->recipeFactory = $recipeFactory;
    }

//    public function create(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
//    {
//        $requestData = $request->getQueryParams();
//
//        if (!$this->validator->validateRequiredKeys($requestData, [
//            'dish_version_id',
//            'name',
//            'quality_id',
//        ])) {
//            $this->responseBuilder->addError('Не указаны обязательные параметры.');
//
//            return $this->responseBuilder->build($response);
//        }
//
//        $data = [
//            'dish_version_id' => intval($requestData['dish_version_id']),
//            'name' => $requestData['name'],
//            'quality_id' => intval($requestData['quality_id']),
//        ];
//
//        $branchID = $this->branchFactory->create($data['dish_version_id'], $data['name']);
//        $this->recipeFactory->_createByBranch($branchID, true);
//
//        $this->responseBuilder->set($branchID);
//
//        return $this->responseBuilder->build($response);
//    }

    public function addProduct(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $requestData = $request->getQueryParams();

        if (!$this->validator->validateRequiredKeys($requestData, [
            'id',
            'reference_product_id',
            'weight',
        ])) {
            return $this->responseBuilder
                ->addError('Не указаны обязательные параметры.')
                ->build($response);
        }

        $data = [
            'id' => intval($requestData['id']),
            'reference_product_id' => intval($requestData['reference_product_id']),
            'weight' => intval($requestData['weight']),
        ];

        $recipe = $this->dataManager->findOneRecipe($data['id']);
        if (!$recipe) {
            return $this->responseBuilder
                ->addError('Рецепт не найден.')
                ->build($response);
        }

        //todo: Проверка возможности редактирования рецепта.

        $recipePosition = $this->dataManager->findRecipePositionByProduct($recipe['id'], $data['reference_product_id']);
        if (!$recipePosition) {
            $insertRecipePositionQuery = 'insert into recipe_positions (weight, recipe_id, reference_product_id) VALUES (:weight, :recipe_id, :reference_product_id)';
            $insertRecipePositionStmt = $this->pdo->prepare($insertRecipePositionQuery);

            $insertRecipePositionStmt->bindValue(':weight', $data['weight']);   //todo: validate
            $insertRecipePositionStmt->bindValue(':recipe_id', $recipe['id']);
            $insertRecipePositionStmt->bindValue(':reference_product_id', $data['reference_product_id']);   //todo: В класс: не понятна ошибка. И рецепты тоже.

            $insertRecipePositionStmt->execute();
        } else {
            $recipePosition['weight'] += $data['weight'];                             //todo: validate

            $updateRecipePositionQuery = 'update recipe_positions set weight = :weight where id = :id';
            $updateRecipePositionStmt = $this->pdo->prepare($updateRecipePositionQuery);

            $updateRecipePositionStmt->bindValue(':weight', $recipePosition['weight']);
            $updateRecipePositionStmt->bindValue(':id', $recipePosition['id']);

            $updateRecipePositionStmt->execute();
        }

        $addedWeight = $data['weight'];
        $this->responseBuilder->set($addedWeight);

        return $this->responseBuilder->build($response);
    }

    public function removeProduct(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $requestData = $request->getQueryParams();

        if (!$this->validator->validateRequiredKeys($requestData, [
            'id',
            'reference_product_id',
            'weight',
        ])) {
            return $this->responseBuilder
                ->addError('Не указаны обязательные параметры.')
                ->build($response);
        }

        $data = [
            'id' => intval($requestData['id']),
            'reference_product_id' => intval($requestData['reference_product_id']),
            'weight' => intval($requestData['weight']),
        ];

        $recipe = $this->dataManager->findOneRecipe($data['id']);
        if (!$recipe) {
            return $this->responseBuilder
                ->addError('Рецепт не найден.')
                ->build($response);
        }

        //todo: Проверка возможности редактирования рецепта.

        $recipePosition = $this->dataManager->findRecipePositionByProduct($recipe['id'], $data['reference_product_id']);
        $removedWeight = 0;
        if ($recipePosition) {
            if ($recipePosition['weight'] >= $data['weight']) {
                $recipePosition['weight'] -= $data['weight'];
                $removedWeight = $data['weight'];
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

        $this->responseBuilder->set($removedWeight);

        return $this->responseBuilder->build($response);
    }

    public function get(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $requestData = $request->getQueryParams();

        if (!$this->validator->validateRequiredKeys($requestData, [
            'id',
        ])) {
            return $this->responseBuilder
                ->addError('Не указаны обязательные параметры.')
                ->build($response);
        }

        $data = [
            'id' => intval($requestData['id']),
        ];

        $recipe = $this->dataManager->findOneRecipe($data['id']);
        if (!$recipe) {
            return $this->responseBuilder
                ->addError('Рецепт не найден.')
                ->build($response);
        }

        $recipePositions = $this->dataManager->findRecipePositions($recipe['id']);
        $recipe['products'] = [];
        foreach ($recipePositions as $recipePosition) {
            $recipe['products'][] = [
                'weight' => $recipePosition['weight'],
                'reference_product_id' => $recipePosition['reference_product_id'],
            ];
        }
        $recipe['head_commit_id'] = $this->dataManager->findHeadRecipeCommit($recipe['id'])['id'] ?? null;

        $this->responseBuilder->set($recipe);

        return $this->responseBuilder->build($response);
    }

    public function commit(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $requestData = $request->getQueryParams();

        if (!$this->validator->validateRequiredKeys($requestData, [
            'id',
        ])) {
            return $this->responseBuilder
                ->addError('Не указаны обязательные параметры.')
                ->build($response);
        }

        $data = [
            'id' => intval($requestData['id']),
        ];

        $recipe = $this->dataManager->findOneRecipe($data['id']);
        if (!$recipe) {
            return $this->responseBuilder
                ->addError('Рецепт не найден.')
                ->build($response);
        }

        $recipePositions = $this->dataManager->findRecipePositions($data['id']);
        if (!count($recipePositions)) {
            return $this->responseBuilder
                ->addError('Рецепт пустой.')
                ->build($response);
        }

        //todo: Проверка наличия изменений для коммита.

        $this->pdo->beginTransaction();

        $previousRecipeCommit = $this->dataManager->findPreviousRecipeCommit($recipe['id']);

        $insertRecipeCommitQuery = 'insert into recipe_commits (recipe_id, previous_commit_id) VALUES (:recipe_id, :previous_commit_id)';
        $insertRecipeCommitStmt = $this->pdo->prepare($insertRecipeCommitQuery);

        $insertRecipeCommitStmt->bindValue(':recipe_id', $recipe['id']);
        $insertRecipeCommitStmt->bindValue(':previous_commit_id', $previousRecipeCommit['id'] ?? null);

        $insertRecipeCommitStmt->execute();
        $recipeCommitID = $this->pdo->lastInsertId();

        $insertCommitRecipePositionQuery = 'insert into recipe_commit_positions (weight, reference_product_id, recipe_commit_id) VALUES (:weight, :reference_product_id, :recipe_commit_id)';
        $insertRecipePositionStmt = $this->pdo->prepare($insertCommitRecipePositionQuery);

        foreach ($recipePositions as $recipePosition) {
            $insertRecipePositionStmt->bindValue(':weight', $recipePosition['weight']);
            $insertRecipePositionStmt->bindValue(':reference_product_id', $recipePosition['reference_product_id']);
            $insertRecipePositionStmt->bindValue(':recipe_commit_id', $recipeCommitID);

            $insertRecipePositionStmt->execute();
        }

        $head = $this->dataManager->findHeadRecipeCommit($recipe['id']);
        if (!$head) {
            $insertHeadQuery = 'insert into heads (recipe_id, recipe_commit_id) values (:recipe_id, :recipe_commit_id)';
            $insertHeadStmt = $this->pdo->prepare($insertHeadQuery);

            $insertHeadStmt->bindValue('recipe_id', $recipe['id']);
            $insertHeadStmt->bindValue('recipe_commit_id', $recipeCommitID);

            $insertHeadStmt->execute();
        } else {
            $updateHeadQuery = 'update heads set recipe_commit_id = :recipe_commit_id where recipe_id = :recipe_id';
            $updateHeadStmt = $this->pdo->prepare($updateHeadQuery);

            $updateHeadStmt->bindValue('recipe_id', $recipe['id']);
            $updateHeadStmt->bindValue('recipe_commit_id', $recipeCommitID);

            $updateHeadStmt->execute();
        }

        $this->pdo->commit();

        return $this->responseBuilder->set($recipeCommitID)->build($response);
    }
}
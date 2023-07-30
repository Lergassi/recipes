<?php

namespace App\Controllers;

use App\Factories\BranchFactory;
use App\Factories\RecipeFactory;
use App\Services\DataManager;
use App\Services\ResponseBuilder;
use App\Services\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class BranchController
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

    public function create(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $requestData = $request->getQueryParams();

        if (!$this->validator->validateRequiredKeys($requestData, [
            'dish_version_id',
            'name',
            'quality_id',
        ])) {
            $this->responseBuilder->addError('Не указаны обязательные параметры.');

            return $this->responseBuilder->build($response);
        }

        $data = [
            'dish_version_id' => intval($requestData['dish_version_id']),
            'name' => $requestData['name'],
            'quality_id' => intval($requestData['quality_id']),
        ];

        $branchID = $this->branchFactory->create($data['dish_version_id'], $data['name']);
        $this->recipeFactory->create($branchID, true);

        $this->responseBuilder->set($branchID);

        return $this->responseBuilder->build($response);
    }

    public function get(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $requestData = $request->getQueryParams();

        if (!$this->validator->validateRequiredKeys($requestData, [
            'id',
        ])) {
            $this->responseBuilder->addError('Не указаны обязательные параметры.');

            return $this->responseBuilder->build($response);
        }

        $data = [
            'id' => intval($requestData['id']),
        ];

        $branch = $this->dataManager->findOneBranch($data['id']);
        if (!$branch) {
            $this->responseBuilder->addError('Ветка не найдена.');

            return $this->responseBuilder->build($response);
        }

        $recipePositions = $this->dataManager->findRecipePositionsByBranch($branch['id']);

        $branch['products'] = [];
        foreach ($recipePositions as $recipePosition) {
            $branch['products'][] = [
                'weight' => $recipePosition['weight'],
                'reference_product_id' => $recipePosition['reference_product_id'],
            ];
        }

        $this->responseBuilder->set($branch);

        return $this->responseBuilder->build($response);
    }

    public function addProduct(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $requestData = $request->getQueryParams();

        if (!$this->validator->validateRequiredKeys($requestData, [
            'branch_id',
            'reference_product_id',
            'weight',
        ])) {
            $this->responseBuilder->addError('Не указаны обязательные параметры.');

            return $this->responseBuilder->build($response);
        }

        $data = [
            'branch_id' => intval($requestData['branch_id']),
            'reference_product_id' => intval($requestData['reference_product_id']),
            'weight' => intval($requestData['weight']),
        ];

        $recipe = $this->dataManager->findMainRecipeByBranch($data['branch_id']);
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
            'branch_id',
            'reference_product_id',
            'weight',
        ])) {
            $this->responseBuilder->addError('Не указаны обязательные параметры.');

            return $this->responseBuilder->build($response);
        }

        $data = [
            'branch_id' => intval($requestData['branch_id']),
            'reference_product_id' => intval($requestData['reference_product_id']),
            'weight' => intval($requestData['weight']),
        ];

        $recipe = $this->dataManager->findMainRecipeByBranch($data['branch_id']);
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

    public function commit(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $requestData = $request->getQueryParams();

        if (!$this->validator->validateRequiredKeys($requestData, [
            'id',
        ])) {
            $this->responseBuilder->addError('Не указаны обязательные параметры.');

            return $this->responseBuilder->build($response);
        }

        $data = [
            'id' => intval($requestData['id']),
        ];

        $recipe = $this->dataManager->findOneRecipe($data['id']);
        $recipePositions = $this->dataManager->findRecipePositionsByBranch($data['id']);
        dump($recipe);
        dump($recipePositions);

        if (!count($recipePositions)) {
            return $this->responseBuilder
                ->addError('Рецепт пустой.')
                ->build($response);
        }

        //todo: Проверка изменений.

        /*
         * Скопировать главный рецепт.
         * Скопировать позиции главного рецепта в скопированный рецепт.
         */

        $this->pdo->beginTransaction();

        $newRecipeId = $this->recipeFactory->create($recipe['dish_version_branch_id']);

        $insertRecipePositionQuery = 'insert into recipe_positions (weight, reference_product_id, recipe_id) VALUES (:weight, :reference_product_id, :recipe_id)';
        $insertRecipePositionStmt = $this->pdo->prepare($insertRecipePositionQuery);
        foreach ($recipePositions as $recipePosition) {
            $insertRecipePositionStmt->bindValue(':weight', $recipePosition['weight']);
            $insertRecipePositionStmt->bindValue(':reference_product_id', $recipePosition['reference_product_id']);
            $insertRecipePositionStmt->bindValue(':recipe_id', $newRecipeId);

            $insertRecipePositionStmt->execute();
        }

//        $this->pdo->commit();

        return $this->responseBuilder->set($newRecipeId)->build($response);
    }
}
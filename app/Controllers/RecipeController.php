<?php

namespace App\Controllers;

use App\Services\DataManager;
use App\Services\ResponseBuilder;
use App\Services\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class RecipeController
{
    private \PDO $pdo;
    private Validator $validator;
    private ResponseBuilder $responseBuilder;
    private DataManager $dataManager;

    public function __construct(\PDO $pdo, ResponseBuilder $responseBuilder, Validator $validator, DataManager $dataManager)
    {
        $this->pdo = $pdo;
        $this->responseBuilder = $responseBuilder;
        $this->validator = $validator;
        $this->dataManager = $dataManager;
    }

//    public function addProduct(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
//    {
//        $requestData = $request->getQueryParams();
//
//        if (!$this->validator->validateRequiredKeys($requestData, [
//            'recipe_id',
//            'reference_product_id',
//            'weight',
//        ])) {
//            $this->responseBuilder->addError('Не указаны обязательные параметры.');
//
//            return $this->responseBuilder->build($response);
//        }
//
//        $data = [
//            'recipe_id' => intval($requestData['recipe_id']),
//            'reference_product_id' => intval($requestData['reference_product_id']),
//            'weight' => intval($requestData['weight']),
//        ];
//
//        $recipe = $this->dataManager->findRecipe($data['recipe_id']);
//        if (!$recipe) {
//            $this->responseBuilder->addError('Рецепт не найден.');
//
//            return $this->responseBuilder->build($response);
//        }
//
//        //todo: Проверка возможности редактирования рецепта.
//
//        $recipePosition = $this->dataManager->findRecipePositionByProduct($data['recipe_id'], $data['reference_product_id']);
//        if (!$recipePosition) {
//            $insertRecipePositionQuery = 'insert into recipe_positions (weight, reference_product_id, recipe_id) VALUES (:weight, :reference_product_id, :recipe_id)';
//            $insertRecipePositionStmt = $this->pdo->prepare($insertRecipePositionQuery);
//            $insertRecipePositionStmt->bindValue(':weight', $data['weight']);   //todo: validate
//            $insertRecipePositionStmt->bindValue(':recipe_id', $data['recipe_id']);
//            $insertRecipePositionStmt->bindValue(':reference_product_id', $data['reference_product_id']);
//            $insertRecipePositionStmt->execute();
//        } else {
//            $recipePosition['weight'] += $data['weight'];                             //todo: validate
//
//            $updateRecipePositionQuery = 'update recipe_positions set weight = :weight where id = :id';
//            $updateRecipePositionStmt = $this->pdo->prepare($updateRecipePositionQuery);
//            $updateRecipePositionStmt->bindValue(':weight', $recipePosition['weight']);
//            $updateRecipePositionStmt->bindValue(':id', $recipePosition['id']);
//            $updateRecipePositionStmt->execute();
//        }
//
//        $addedWeight = $data['weight'];
//        $this->responseBuilder->set($addedWeight);
//
//        return $this->responseBuilder->build($response);
//    }
//
//    public function removeProduct(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
//    {
//        $requestData = $request->getQueryParams();
//
//        if (!$this->validator->validateRequiredKeys($requestData, [
//            'recipe_id',
//            'reference_product_id',
//            'weight',
//        ])) {
//            $this->responseBuilder->addError('Не указаны обязательные параметры.');
//
//            return $this->responseBuilder->build($response);
//        }
//
//        $data = [
//            'recipe_id' => intval($requestData['recipe_id']),
//            'reference_product_id' => intval($requestData['reference_product_id']),
//            'weight' => intval($requestData['weight']),
//        ];
//
//        $recipe = $this->dataManager->findRecipe($data['recipe_id']);
//        if (!$recipe) {
//            $this->responseBuilder->addError('Рецепт не найден.');
//
//            return $this->responseBuilder->build($response);
//        }
//
//        //todo: Проверка возможности редактирования рецепта.
//
//        $recipePosition = $this->dataManager->findRecipePositionByProduct($data['recipe_id'], $data['reference_product_id']);
//        $removedWeight = 0;
//        if ($recipePosition) {
//            if ($recipePosition['weight'] >= $data['weight']) {
//                $recipePosition['weight'] -= $data['weight'];
//                $removedWeight = $data['weight'];
//            } else {
//                $removedWeight = $recipePosition['weight'];
//                $recipePosition['weight'] = 0 ;
//            }
//
//            $updateRecipePositionQuery = 'update recipe_positions set weight = :weight where id = :id';
//            $updateRecipePositionStmt = $this->pdo->prepare($updateRecipePositionQuery);
//            $updateRecipePositionStmt->bindValue(':weight', $recipePosition['weight']);
//            $updateRecipePositionStmt->bindValue(':id', $recipePosition['id']);
//            $updateRecipePositionStmt->execute();
//        }
//
//        $this->responseBuilder->set($removedWeight);
//
//        return $this->responseBuilder->build($response);
//    }

//    public function updateProduct(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface {return $response;}
}
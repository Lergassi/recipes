<?php

namespace App\Controllers;

use App\Factories\BranchFactory;
use App\Factories\RecipeFactory;
use App\Services\DataManager;
use App\Services\RecipeService;
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
    private RecipeService $recipeService;

    public function __construct(
        \PDO            $pdo,
        ResponseBuilder $responseBuilder,
        Validator       $validator,
        DataManager     $dataManager,
        BranchFactory   $branchFactory,
        RecipeFactory   $recipeFactory,
        RecipeService   $recipeService,
    )
    {
        $this->pdo = $pdo;
        $this->responseBuilder = $responseBuilder;
        $this->validator = $validator;
        $this->dataManager = $dataManager;
        $this->branchFactory = $branchFactory;
        $this->recipeFactory = $recipeFactory;
        $this->recipeService = $recipeService;
    }

    public function create(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $requestData = $request->getQueryParams();

        if (!$this->validator->validateRequiredKeys($requestData, [
            'name',
            'dish_version_id',
        ])) {
            $this->responseBuilder->addError('Не указаны обязательные параметры.');

            return $this->responseBuilder->build($response);
        }

        $data = [
            'name' => $requestData['name'],
            'dish_version_id' => intval($requestData['dish_version_id']),
            'quality_id' => isset($requestData['quality_id']) ? intval($requestData['quality_id']) : $this->dataManager->findOneQualityByAlias('common')['id'],
        ];

        $recipeID = $this->recipeFactory->create($data['name'], $data['dish_version_id']);

        $this->responseBuilder->set($recipeID);

        return $this->responseBuilder->build($response);
    }

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

        $addedWeight = $this->recipeService->addProduct($data['id'], $data['reference_product_id'], $data['weight']);
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

        $this->responseBuilder->set($this->recipeService->removeProduct($data['id'], $data['reference_product_id'], $data['weight']));

        return $this->responseBuilder->build($response);
    }

    public function all(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $requestData = $request->getQueryParams();

        if (!$this->validator->validateRequiredKeys($requestData, [
            'dish_version_id',
        ])) {
            return $this->responseBuilder
                ->addError('Не указаны обязательные параметры.')
                ->build($response);
        }

        $data = [
            'dish_version_id' => intval($requestData['dish_version_id']),
        ];

        $recipes = $this->dataManager->findRecipes($data['dish_version_id']);

        $this->responseBuilder->set($recipes);

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

        //todo: Запросы на получение данных для api можно сделать отдельно.
        $recipePositions = $this->dataManager->findRecipePositions($recipe['id']);
        $recipe['products'] = [];
        foreach ($recipePositions as $recipePosition) {
            $recipe['products'][] = [
                'reference_product' => [
                    'id' => $recipePosition['reference_product_id'],
                    'name' => $recipePosition['reference_product_name'],
                ],
                'weight' => $recipePosition['weight'],
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

//        $insertRecipeCommitQuery = 'insert into recipe_commits (recipe_id, previous_commit_id) VALUES (:recipe_id, :previous_commit_id)';
//        $insertRecipeCommitStmt = $this->pdo->prepare($insertRecipeCommitQuery);
//
//        $insertRecipeCommitStmt->bindValue(':recipe_id', $recipe['id']);
//        $insertRecipeCommitStmt->bindValue(':previous_commit_id', $previousRecipeCommit['id'] ?? null);
//
//        $insertRecipeCommitStmt->execute();
//        $recipeCommitID = $this->pdo->lastInsertId();
//
//        $insertRecipeCommitPositionQuery = 'insert into recipe_commit_positions (weight, reference_product_id, recipe_commit_id) VALUES (:weight, :reference_product_id, :recipe_commit_id)';
//        $insertRecipeCommitPositionStmt = $this->pdo->prepare($insertRecipeCommitPositionQuery);
//
//        foreach ($recipePositions as $recipePosition) {
//            $insertRecipeCommitPositionStmt->bindValue(':weight', $recipePosition['weight']);
//            $insertRecipeCommitPositionStmt->bindValue(':reference_product_id', $recipePosition['reference_product_id']);
//            $insertRecipeCommitPositionStmt->bindValue(':recipe_commit_id', $recipeCommitID);
//
//            $insertRecipeCommitPositionStmt->execute();
//        }
//
//        $this->recipeService->updateHead($recipe['id'], $recipeCommitID);

        $recipeCommitID = $this->recipeService->commit($recipe['id'], $previousRecipeCommit['id'] ?? null);

        $this->pdo->commit();

        return $this->responseBuilder->set($recipeCommitID)->build($response);
    }

    public function branch(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $requestData = $request->getQueryParams();

        if (!$this->validator->validateRequiredKeys($requestData, [
            'id',
            'name',
        ])) {
            return $this->responseBuilder
                ->addError('Не указаны обязательные параметры.')
                ->build($response);
        }

        $data = [
            'id' => intval($requestData['id']),
            'name' => $requestData['name'],
        ];

        $recipe = $this->dataManager->findOneRecipe($data['id']);
        if (!$recipe) {
            return $this->responseBuilder
                ->addError('Рецепт не найден.')
                ->build($response);
        }

        $head = $this->dataManager->findHeadRecipeCommit($recipe['id']);
        if (!$head) {
            return $this->responseBuilder
                ->addError('Нельзя создать рецепт. В рецепте нет ни одного зафиксированного изменения.')
                ->build($response);
        }

//        dump($head);
//        dump($this->dataManager->findCommitRecipePositions($head['id']));
//        dump($this->dataManager->findRecipePositions($recipe['id']));
//        dump($this->dataManager->findDiffWithCurrentRecipe($recipe['id']));
//        dd($this->dataManager->hasDiffWithCurrentRecipe($recipe['id']));
        if ($this->dataManager->hasDiffWithCurrentRecipe($recipe['id'])) {
            return $this->responseBuilder
                ->addError('Нельзя создать рецепт. Текущий рецепт имеет незафиксированные изменения.')
                ->build($response);
        }

        $this->pdo->beginTransaction();

        $newRecipeID = $this->recipeFactory->create($data['name'], $recipe['dish_version_id']);

        $recipePositions = $this->dataManager->findRecipePositions($recipe['id']);
        foreach ($recipePositions as $recipePosition) {
            $this->recipeService->addProduct($newRecipeID, $recipePosition['reference_product_id'], $recipePosition['weight']);
        }

        $this->recipeService->commit($newRecipeID);

//        $this->recipeService->updateHead($newRecipeID, $head['id']);

//        $newRecipeCommitID = $this->recipeService->copyRecipeCommit($head['id']);
//        $this->recipeService->updateHead($newRecipeID, $newRecipeCommitID);

        $this->pdo->commit();

        $this->responseBuilder->set($newRecipeID);

        return $this->responseBuilder->build($response);
    }
}
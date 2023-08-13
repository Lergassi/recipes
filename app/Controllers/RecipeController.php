<?php

namespace App\Controllers;

use App\Factories\RecipeFactory;
use App\Services\DataManager;
use App\Services\RecipeService;
use App\Services\ResponseBuilder;
use App\Services\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Required;

class RecipeController
{
    private \PDO $pdo;
    private RecipeFactory $recipeFactory;
    private Validator $validator;
    private ResponseBuilder $responseBuilder;
    private DataManager $dataManager;
    private RecipeService $recipeService;

    public function __construct(
        \PDO             $pdo,
        ResponseBuilder  $responseBuilder,
        Validator $validator,
        DataManager      $dataManager,
        RecipeFactory    $recipeFactory,
        RecipeService    $recipeService,
    )
    {
        $this->pdo = $pdo;
        $this->responseBuilder = $responseBuilder;
        $this->validator = $validator;
        $this->dataManager = $dataManager;
        $this->recipeFactory = $recipeFactory;
        $this->recipeService = $recipeService;
    }

    public function create(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $requestData = $request->getQueryParams();
        if ($this->validator->validate($requestData, [
            new Collection([
                'fields' => [
                    'name' => new Required([
                        new NotBlank(['allowNull' => false]),
                    ]),
                    'dish_version_id' => new Required([
                        new NotBlank(['allowNull' => false]),
                    ]),
                ],
                'allowExtraFields' => true,
            ]),
        ], $this->responseBuilder)) return $this->responseBuilder
            ->build($response);

        $data = [
            'name' => $requestData['name'],
            'dish_version_id' => intval($requestData['dish_version_id']),
        ];

        $recipeID = $this->recipeFactory->create($data['name'], $data['dish_version_id']);

        $this->responseBuilder->set($recipeID);

        return $this->responseBuilder->build($response);
    }

    public function addProduct(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $requestData = $request->getQueryParams();
        if ($this->validator->validate($requestData, [
            new Collection([
                'fields' => [
                    'id' => new Required([
                        new NotBlank(['allowNull' => false]),
                    ]),
                    'reference_product_id' => new Required([
                        new NotBlank(['allowNull' => false]),
                    ]),
                    'weight' => new Required([
                        new NotBlank(['allowNull' => false]),
                    ]),
                ],
                'allowExtraFields' => true,
            ]),
        ], $this->responseBuilder)) return $this->responseBuilder
            ->build($response);

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
        //todo: Одинаковая логика с add_product.
        if ($this->validator->validate($requestData, [
            new Collection([
                'fields' => [
                    'id' => new Required([
                        new NotBlank(['allowNull' => false]),
                    ]),
                    'reference_product_id' => new Required([
                        new NotBlank(['allowNull' => false]),
                    ]),
                    'weight' => new Required([
                        new NotBlank(['allowNull' => false]),
                    ]),
                ],
                'allowExtraFields' => true,
            ]),
        ], $this->responseBuilder)) return $this->responseBuilder
            ->build($response);

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
        if ($this->validator->validate($requestData, [
            new Collection([
                'fields' => [
                    'dish_version_id' => new Required([
                        new NotBlank(['allowNull' => false]),
                    ]),
                ],
                'allowExtraFields' => true,
            ]),
        ], $this->responseBuilder)) return $this->responseBuilder
            ->build($response);

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
        if ($this->validator->validate($requestData, [
            new Collection([
                'fields' => [
                    'id' => new Required([
                        new NotBlank(['allowNull' => false]),
                    ]),
                ],
                'allowExtraFields' => true,
            ]),
        ], $this->responseBuilder)) return $this->responseBuilder
            ->build($response);

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
        if ($this->validator->validate($requestData, [
            new Collection([
                'fields' => [
                    'id' => new Required([
                        new NotBlank(['allowNull' => false]),
                    ]),
                ],
                'allowExtraFields' => true,
            ]),
        ], $this->responseBuilder)) return $this->responseBuilder
            ->build($response);

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

        $recipeCommitID = $this->recipeService->commit($recipe['id'], $previousRecipeCommit['id'] ?? null);

        $this->pdo->commit();

        return $this->responseBuilder->set($recipeCommitID)->build($response);
    }

    public function branch(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $requestData = $request->getQueryParams();
        if ($this->validator->validate($requestData, [
            new Collection([
                'fields' => [
                    'id' => new Required([
                        new NotBlank(['allowNull' => false]),
                    ]),
                    'name' => new Required([
                        new NotBlank(['allowNull' => false]),
                    ]),
                ],
                'allowExtraFields' => true,
            ]),
        ], $this->responseBuilder)) return $this->responseBuilder
            ->build($response);

        $data = [
            'id' => intval($requestData['id']),
            'name' => $requestData['name'],
        ];

        if ($this->validator->validate($data, new Collection([
            'fields' => [
                'name' => new Required([
                    new Length(['min' => 1, 'max' => 64]),
                ]),
                //alias unique
            ],
            'allowExtraFields' => true,
        ]), $this->responseBuilder)) return $this->responseBuilder->build($response);

        $recipe = $this->dataManager->findOneRecipe($data['id']);
        if (!$recipe) {
            return $this->responseBuilder
                ->addError('Рецепт не найден.')
                ->build($response);
        }

        $head = $this->dataManager->findHeadRecipeCommit($recipe['id']);
        if (!$head) {
            return $this->responseBuilder
                ->addError('Нельзя создать ветку. В рецепте нет ни одного зафиксированного изменения.')
                ->build($response);
        }

        if ($this->dataManager->hasDiffWithCurrentRecipe($recipe['id'])) {
            return $this->responseBuilder
                ->addError('Нельзя создать ветку. Текущий рецепт имеет незафиксированные изменения.')
                ->build($response);
        }

        $this->pdo->beginTransaction();

        $newRecipeID = $this->recipeFactory->create($data['name'], $recipe['dish_version_id']);

        $recipePositions = $this->dataManager->findRecipePositions($recipe['id']);
        foreach ($recipePositions as $recipePosition) {
            $this->recipeService->addProduct($newRecipeID, $recipePosition['reference_product_id'], $recipePosition['weight']);
        }

        $this->recipeService->commit($newRecipeID);

        $this->pdo->commit();

        $this->responseBuilder->set($newRecipeID);

        return $this->responseBuilder->build($response);
    }
}
<?php

namespace App\Controller;

use App\DataManager\CommitManager;
use App\DataManager\RecipeManager;
use App\DataManager\RecipePositionManager;
use App\Factory\ExistsConstraintFactory;
use App\Factory\RecipeFactory;
use App\Service\DataManager;
use App\Service\RecipeService;
use App\Service\ResponseBuilder;
use App\Service\Validation\Validator;
use DI\Attribute\Inject;
use PDO;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Required;

class RecipeController
{
    #[Inject] private PDO $pdo;
    #[Inject] private RecipeFactory $recipeFactory;
    #[Inject] private Validator $validator;
    #[Inject] private ResponseBuilder $responseBuilder;
    #[Inject] private DataManager $dataManager;
    #[Inject] private RecipePositionManager $recipePositionManager;
    #[Inject] private RecipeManager $recipeManager;
    #[Inject] private CommitManager $commitManager;
    #[Inject] private RecipeService $recipeService;
    #[Inject] private ExistsConstraintFactory $existsConstraintFactory;

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

        if ($this->validator->validate($data, new Collection([
            'fields' => [
                'name' => new Required([
                    new Length(['min' => 1, 'max' => 256]),
                ]),
                'dish_version_id' => new Required([
                    $this->existsConstraintFactory->create([
                        'table' => 'dish_versions',
                    ]),
                ]),
            ],
            'allowExtraFields' => true,
        ]), $this->responseBuilder)) return $this->responseBuilder->build($response);

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

        $recipe = $this->recipeManager->findOne($data['id']);
        if (!$recipe) return $this->responseBuilder
            ->addError('Рецепт не найден.')
            ->build($response);

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

        $recipe = $this->recipeManager->findOne($data['id']);
        if (!$recipe) return $this->responseBuilder
            ->addError('Рецепт не найден.')
            ->build($response);

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

        $recipes = $this->recipeManager->findByDishVersion($data['dish_version_id']);

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

        $recipe = $this->recipeManager->findOne($data['id']);
        if (!$recipe) return $this->responseBuilder
            ->addError('Рецепт не найден.')
            ->build($response);

        //todo: Запросы на получение данных для api можно сделать отдельно.
        $recipePositions = $this->recipePositionManager->findByRecipe($recipe['id']);
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
        $recipe['head_commit_id'] = $this->commitManager->findHeadRecipeCommit($recipe['id'])['id'] ?? null;

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

        $recipe = $this->recipeManager->findOne($data['id']);
        if (!$recipe) return $this->responseBuilder
            ->addError('Рецепт не найден.')
            ->build($response);

        //todo: Конструктор?
        $recipePositions = $this->recipePositionManager->findByRecipe($data['id']);
        if (!count($recipePositions)) return $this->responseBuilder
            ->addError('Рецепт пустой.')
            ->build($response);

        //todo: Проверка наличия изменений для коммита. Если изменений нет - коммит не делать.

        $this->pdo->beginTransaction();

        $previousRecipeCommit = $this->commitManager->findOnePreviousRecipeCommit($recipe['id']);

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
            ],
            'allowExtraFields' => true,
        ]), $this->responseBuilder)) return $this->responseBuilder->build($response);

        $recipe = $this->recipeManager->findOne($data['id']);
        if (!$recipe) return $this->responseBuilder
            ->addError('Рецепт не найден.')
            ->build($response);

        $head = $this->commitManager->findHeadRecipeCommit($recipe['id']);
        if (!$head) return $this->responseBuilder
            ->addError('Нельзя создать ветку. В рецепте нет ни одного зафиксированного изменения.')
            ->build($response);

        if ($this->commitManager->hasDiffWithCurrentRecipe($recipe['id'])) return $this->responseBuilder
            ->addError('Нельзя создать ветку. Текущий рецепт имеет незафиксированные изменения.')
            ->build($response);

        $this->pdo->beginTransaction();

        $newRecipeID = $this->recipeFactory->create($data['name'], $recipe['dish_version_id']);

        $recipePositions = $this->recipePositionManager->findByRecipe($recipe['id']);
        foreach ($recipePositions as $recipePosition) {
            $this->recipeService->addProduct($newRecipeID, $recipePosition['reference_product_id'], $recipePosition['weight']);
        }

        $this->recipeService->commit($newRecipeID);

        $this->pdo->commit();

        $this->responseBuilder->set($newRecipeID);

        return $this->responseBuilder->build($response);
    }
}
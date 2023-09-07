<?php

namespace App\Controller;

use App\DataManager\CommitManager;
use App\DataManager\DishVersionManager;
use App\DataManager\RecipeManager;
use App\Factory\ExistsConstraintFactory;
use App\Factory\RecipeFactory;
use App\Factory\UniqueConstraintFactory;
use App\Service\DataManager;
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

class DishVersionController
{
    #[Inject] private PDO $pdo;
    #[Inject] private RecipeFactory $recipeFactory;
    #[Inject] private Validator $validator;
    #[Inject] private ResponseBuilder $responseBuilder;
    #[Inject] private DataManager $dataManager;
    #[Inject] private DishVersionManager $dishVersionManager;
    #[Inject] private RecipeManager $recipeManager;
    #[Inject] private CommitManager $commitManager;
    #[Inject] private UniqueConstraintFactory $uniqueConstraintFactory;
    #[Inject] private ExistsConstraintFactory $existsConstraintFactory;

    public function create(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $requestData = $request->getQueryParams();
        if ($this->validator->validate($requestData, [
            //todo: Возможно стоит сделать отдельный валидатор для простого наличия всех полей.
            new Collection([
                'fields' => [
                    'name' => new Required([
                        new NotBlank(['allowNull' => false]),
                    ]),
                    'alias' => new Required([
                        new NotBlank(['allowNull' => false]),
                    ]),
                    'dish_id' => new Required([
                        new NotBlank(['allowNull' => false]),
                    ]),
                    'quality_id' => new Required([
                        new NotBlank(['allowNull' => false]),
                    ]),
                ],
                'allowExtraFields' => true,
            ]),
        ], $this->responseBuilder)) return $this->responseBuilder
            ->build($response);

        $data = [
            'name' => $requestData['name'],
            'alias' => $requestData['alias'],
            'dish_id' => intval($requestData['dish_id']),
            'quality_id' => intval($requestData['quality_id']),
        ];

        if ($this->validator->validate($data, new Collection([
            'fields' => [
                'name' => new Required([
                    new Length(['min' => 1, 'max' => 128]),
                ]),
                'alias' => new Required([
                    new Length(['min' => 1, 'max' => 150]),
                    $this->uniqueConstraintFactory->create([
                        'table' => 'dish_versions',
                        'column' => 'alias',
                    ]),
                ]),
                'dish_id' => new Required([
                    $this->existsConstraintFactory->create([
                        'table' => 'dishes',
                    ]),
                ]),
                'quality_id' => new Required([
                    $this->existsConstraintFactory->create([
                        'table' => 'qualities',
                    ]),
                ]),
            ],
            'allowExtraFields' => true,
        ]), $this->responseBuilder)) return $this->responseBuilder->build($response);

        $this->pdo->beginTransaction();

        $query = 'insert into dish_versions (name, alias, dish_id, quality_id) values (:name, :alias, :dish_id, :quality_id)';

        $stmt = $this->pdo->prepare($query);

        $stmt->bindValue(':name', $data['name']);
        $stmt->bindValue(':alias', $data['alias']);
        $stmt->bindValue(':dish_id', $data['dish_id']);
        $stmt->bindValue(':quality_id', $data['quality_id']);

        $stmt->execute();

        $dishVersionID = $this->pdo->lastInsertId();

        $this->recipeFactory->create('Оригинальный', $dishVersionID);

        $this->pdo->commit();

        $this->responseBuilder->set($dishVersionID);

        return $this->responseBuilder->build($response);
    }

    public function all(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $requestData = $request->getQueryParams();
        if ($this->validator->validate($requestData, [
            new Collection([
                'fields' => [
                    'dish_id' => new Required([
                        new NotBlank(['allowNull' => false]),
                    ]),
                ],
                'allowExtraFields' => true,
            ]),
        ], $this->responseBuilder)) return $this->responseBuilder
            ->build($response);

        //todo: Совместить с get логикой.
        $dishVersions = $this->dishVersionManager->findByDish(intval($requestData['dish_id']));
//        foreach ($dishVersions as &$dishVersion) {
//            $dishVersion['recipes'] = $this->dataManager->findRecipes($dishVersion['id']);
//            foreach ($dishVersion['recipes'] as &$recipe) {
//                $recipe['head_commit_id'] = $this->dataManager->findHeadRecipeCommit($recipe['id'])['id'] ?? null;
//            }
//        }

        $this->responseBuilder->set($dishVersions);

        return $this->responseBuilder->build($response);
    }

    public function get(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $requestData = $request->getQueryParams();
        if ($this->validator->validate($requestData, [
            new Collection([
                'fields' => [
                    'dish_id' => new Required([
                        new NotBlank(['allowNull' => false]),
                    ]),
                ],
                'allowExtraFields' => true,
            ]),
        ], $this->responseBuilder)) return $this->responseBuilder->build($response);

        $data = [
            'id' => intval($requestData['id']),
        ];

        $dishVersion = $this->dishVersionManager->findOne($data['id']);
        if (!$dishVersion) return $this->responseBuilder
            ->addError('Версия блюда не найдена.')
            ->build($response);

        //todo: Убрать.
        $dishVersion['recipes'] = $this->recipeManager->findByDishVersion($dishVersion['id']);
        foreach ($dishVersion['recipes'] as &$recipe) {
            $recipe['head_commit_id'] = $this->commitManager->findHeadRecipeCommit($recipe['id'])['id'] ?? null;
        }

        $this->responseBuilder->set($dishVersion);

        return $this->responseBuilder->build($response);
    }

    public function update(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
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
                    'alias' => new Required([
                        new NotBlank(['allowNull' => false]),
                    ]),
                    'quality_id' => new Required([
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
            'alias' => $requestData['alias'],
            'quality_id' => intval($requestData['quality_id']),
        ];

        if ($this->validator->validate($data, new Collection([
            'fields' => [
                'name' => new Required([
                    new Length(['min' => 1, 'max' => 128]),
                ]),
                'alias' => new Required([
                    new Length(['min' => 1, 'max' => 150]),
                    $this->uniqueConstraintFactory->create([
                        'table' => 'dish_versions',
                        'column' => 'alias',
                    ]),
                ]),
                'quality_id' => new Required([
                    $this->existsConstraintFactory->create([
                        'table' => 'qualities',
                    ]),
                ]),
            ],
            'allowExtraFields' => true,
        ]), $this->responseBuilder)) return $this->responseBuilder->build($response);

        $query = 'update dish_versions set name = :name, alias = :alias, quality_id = :quality_id where id = :id';

        $stmt = $this->pdo->prepare($query);

        $stmt->bindValue(':id', $data['id']);
        $stmt->bindValue(':name', $data['name']);
        $stmt->bindValue(':alias', $data['alias']);
        $stmt->bindValue(':quality_id', $data['quality_id']);

        $stmt->execute();

        $this->responseBuilder->set($stmt->rowCount());
        $response = $this->responseBuilder->build($response);

        return $response;
    }

    public function delete(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
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

        $ID = intval($request->getQueryParams()['id']);

        //todo: validate data
        //todo: удаление рецептов и коммитов

        $query = 'delete from dish_versions where id = :id';

        $stmt = $this->pdo->prepare($query);

        $stmt->bindValue('id', $ID);

        $stmt->execute();

        $this->responseBuilder->set($stmt->rowCount());

        return $this->responseBuilder->build($response);
    }
}
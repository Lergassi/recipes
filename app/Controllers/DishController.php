<?php

namespace App\Controllers;

use App\Factories\ExistsConstraintFactory;
use App\Factories\UniqueConstraintFactory;
use App\Services\DataManager;
use App\Services\ResponseBuilder;
use App\Services\Validation\Validator;
use PDO;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Required;

class DishController
{
    private PDO $pdo;
    private DataManager $dataManager;
    private UniqueConstraintFactory $uniqueConstraintFactory;
    private ExistsConstraintFactory $existsConstraintFactory;
    private ResponseBuilder $responseBuilder;
    private Validator $validator;

    public function __construct(
        PDO                     $pdo,
        ResponseBuilder         $responseBuilder,
        Validator               $validator,
        DataManager             $dataManager,
        UniqueConstraintFactory $uniqueConstraintFactory,
        ExistsConstraintFactory $existsConstraintFactory,
    )
    {
        $this->pdo = $pdo;
        $this->responseBuilder = $responseBuilder;
        $this->validator = $validator;
        $this->dataManager = $dataManager;
        $this->uniqueConstraintFactory = $uniqueConstraintFactory;
        $this->existsConstraintFactory = $existsConstraintFactory;
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
            'name' => $requestData['name'],
            'alias' => $requestData['alias'],
            'quality_id' => $requestData['quality_id']
        ];

        if ($this->validator->validate($data, new Collection([
            'fields' => [
                'name' => new Required([
                    new Length(['min' => 1, 'max' => 128]),
                ]),
                'alias' => new Required([
                    new Length(['min' => 1, 'max' => 100]),
                    $this->uniqueConstraintFactory->create([
                        'table' => 'dishes',
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

        $query = 'insert into dishes (name, alias, quality_id) values (:name, :alias, :quality_id)';

        $stmt = $this->pdo->prepare($query);

        $stmt->bindValue(':name', $data['name']);
        $stmt->bindValue(':alias', $data['alias']);
        $stmt->bindValue(':quality_id', $data['quality_id']);

        $stmt->execute();

        $this->responseBuilder->set($this->pdo->lastInsertId());

        return $this->responseBuilder->build($response);
    }

    public function all(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $dishes = $this->dataManager->findDishes();

        foreach ($dishes as &$dish) {
            $dishVersions = $this->dataManager->findDishVersions($dish['id']);
            $dish['versions'] = $dishVersions;
        }

        $this->responseBuilder->set($dishes);

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

        $dish = $this->dataManager->findOneDish(intval($requestData['id']));
        if (!$dish) {
            return $this->responseBuilder
                ->addError('Блюдо не найдено.')
                ->build($response);
        }

        $dishVersions = $this->dataManager->findDishVersions($dish['id']);
        $dish['versions'] = $dishVersions;

        $this->responseBuilder->set($dish);

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
                    new Length(['min' => 1, 'max' => 100]),
                    $this->uniqueConstraintFactory->create([
                        'table' => 'dishes',
                        'column' => 'alias',
                        'existsID' => $data['id'],
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

        $query = 'update dishes set name = :name, alias = :alias, quality_id = :quality_id where id = :id';

        $stmt = $this->pdo->prepare($query);

        $stmt->bindValue(':id', $data['id']);
        $stmt->bindValue(':name', $data['name']);
        $stmt->bindValue(':alias', $data['alias']);
        $stmt->bindValue(':quality_id', $data['quality_id']);

        $stmt->execute();

        $this->responseBuilder->set($stmt->rowCount());

        return $this->responseBuilder->build($response);
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
        //todo: validate foreign keys
        /*
         * dishes
         * dish_versions
         * recipes
         * recipe_positions
         *
         * recipe_commits
         * recipe_commit_positions
         * heads
         * */

        $query = 'delete from dishes where id = :id';

        $stmt = $this->pdo->prepare($query);

        $stmt->bindValue('id', $ID);

        $stmt->execute();

        $this->responseBuilder->set($stmt->rowCount());

        return $this->responseBuilder->build($response);
    }
}
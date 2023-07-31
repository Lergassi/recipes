<?php

namespace App\Controllers;

use App\Services\AliasGenerator;
use App\Services\DataManager;
use App\Services\ResponseBuilder;
use App\Services\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class DishController
{
    private \PDO $pdo;
    private DataManager $dataManager;
    private AliasGenerator $aliasGenerator;
    private ResponseBuilder $responseBuilder;
    private Validator $validator;

    public function __construct(
        \PDO            $pdo,
        ResponseBuilder $responseBuilder,
        Validator       $validator,
        AliasGenerator  $aliasGenerator,
        DataManager     $dataManager,
    )
    {
        $this->pdo = $pdo;
        $this->responseBuilder = $responseBuilder;
        $this->validator = $validator;
        $this->aliasGenerator = $aliasGenerator;
        $this->dataManager = $dataManager;
    }

    public function create(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $requestData = $request->getQueryParams();

        if (!$this->validator->validateRequiredKeys($requestData, [
            'name',
        ])) {
            return $this->responseBuilder
                ->addError('Не указаны обязательные параметры.')
                ->build($response);
        }

        $data = [
            'name' => $requestData['name'],
            'alias' => $requestData['alias'] ?? $this->aliasGenerator->generate($requestData['name'], 1),
            'quality_id' => isset($requestData['quality_id']) ? intval($requestData['quality_id']) : $this->dataManager->findOneQualityByAlias('common')['id'], //todo: Сделать значения по умолчанию и/или удобное использование alias в коде.
        ];

        //todo: validate data

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

        if (!$this->validator->validateRequiredKeys($requestData, [
            'id',
        ])) {
            return $this->responseBuilder
                ->addError('Не указаны обязательные параметры.')
                ->build($response);
        }

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

        if (!$this->validator->validateRequiredKeys($requestData, [
            'id',
            'name',
            'alias',
            'quality_id',
        ])) {
            return $this->responseBuilder
                ->addError('Не указаны обязательные параметры.')
                ->build($response);
        }

        $data = [
            'id' => intval($requestData['id']),
            'name' => $requestData['name'],
            'alias' => $requestData['alias'],
            'quality_id' => intval($requestData['quality_id']),
        ];

        //todo: validate data

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

        if (!$this->validator->validateRequiredKeys($requestData, [
            'id',
        ])) {
            return $this->responseBuilder
                ->addError('Не указаны обязательные параметры.')
                ->build($response);
        }

        $ID = intval($request->getQueryParams()['id']);

        //todo: validate data
        //todo: validate foreign keys

        $query = 'delete from dishes where id = :id';

        $stmt = $this->pdo->prepare($query);

        $stmt->bindValue('id', $ID);

        $stmt->execute();

        $this->responseBuilder->set($stmt->rowCount());

        return $this->responseBuilder->build($response);
    }
}
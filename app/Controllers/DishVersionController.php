<?php

namespace App\Controllers;

use App\Services\AliasGenerator;
use App\Services\ResponseBuilder;
use App\Services\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class DishVersionController
{
    private \PDO $pdo;
    private ResponseBuilder $responseBuilder;
    private Validator $validator;
    private AliasGenerator $aliasGenerator;

    public function __construct(\PDO $pdo, AliasGenerator $aliasGenerator, ResponseBuilder $responseBuilder, Validator $validator)
    {
        $this->pdo = $pdo;
        $this->responseBuilder = $responseBuilder;
        $this->validator = $validator;
        $this->aliasGenerator = $aliasGenerator;
    }

    public function create(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $requestData = $request->getQueryParams();

        if (!$this->validator->validateRequiredKeys($requestData, [
            'name',
            'dish_id',
            'quality_id',
        ])) {
            $this->responseBuilder->addError('Не указаны обязательные параметры.');

            return $this->responseBuilder->build($response);
        }

        $data = [
            'name' => $requestData['name'],
            'alias' => $requestData['alias'] ?? $this->aliasGenerator->generate($requestData['name'], 1),   //todo: index получить на основе бд.
            'dish_id' => intval($requestData['dish_id']),
            'quality_id' => intval($requestData['quality_id']),
        ];
        //todo: validate data

        $query = 'insert into dish_versions (name, alias, dish_id, quality_id) values (:name, :alias, :dish_id, :quality_id)';

        $stmt = $this->pdo->prepare($query);

        $stmt->bindValue(':name', $data['name']);
        $stmt->bindValue(':alias', $data['alias']);
        $stmt->bindValue(':dish_id', $data['dish_id']);
        $stmt->bindValue(':quality_id', $data['quality_id']);

        $stmt->execute();

        $this->responseBuilder->set($this->pdo->lastInsertId());

        return $this->responseBuilder->build($response);
    }

    public function get(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $query = 'select dv.id, dv.name, d.id as d_id, dv.alias, dv.quality_id from dish_versions dv left join dishes d on dv.dish_id = d.id left join qualities q on d.quality_id = q.id order by d.name ASC, dv.name, q.sort';

        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        $result = $stmt->fetchAll();

        $this->responseBuilder->set($result);

        $response = $this->responseBuilder->build($response);

        return $response;
    }

    public function update(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $requestData = $request->getQueryParams();

        if (!$this->validator->validateRequiredKeys($requestData, [
            'id',
            'name',
            'dish_id',
            'quality_id',
        ])) {
            $this->responseBuilder->addError('Не указаны обязательные параметры.');

            return $this->responseBuilder->build($response);
        }

        $data = [
            'id' => intval($requestData['id']),
            'name' => $requestData['name'],
            'alias' => $requestData['alias'] ?? $this->aliasGenerator->generate($requestData['name'], 1),
            'dish_id' => intval($requestData['dish_id']),
            'quality_id' => intval($requestData['quality_id']),
        ];

        //todo: validate data

        $query = 'update dish_versions set name = :name, alias = :alias, dish_id = :dish_id, quality_id = :quality_id where id = :id';

        $stmt = $this->pdo->prepare($query);

        $stmt->bindValue(':id', $data['id']);
        $stmt->bindValue(':name', $data['name']);
        $stmt->bindValue(':alias', $data['alias']);
        $stmt->bindValue(':dish_id', $data['dish_id']);
        $stmt->bindValue(':quality_id', $data['quality_id']);

        $stmt->execute();

        $this->responseBuilder->set($stmt->rowCount());
        $response = $this->responseBuilder->build($response);

        return $response;
    }

    public function delete(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $requestData = $request->getQueryParams();

        if (!$this->validator->validateRequiredKeys($requestData, [
            'id',
        ])) {
            $this->responseBuilder->addError('Не указаны обязательные параметры.');

            return $this->responseBuilder->build($response);
        }

        $ID = intval($request->getQueryParams()['id']);

        //todo: validate data
        //todo: validate foreign keys

        $query = 'delete from dish_versions where id = :id';

        $stmt = $this->pdo->prepare($query);

        $stmt->bindValue('id', $ID);

        $stmt->execute();

        $this->responseBuilder->set($stmt->rowCount());
        $response = $this->responseBuilder->build($response);

        return $response;
    }
}
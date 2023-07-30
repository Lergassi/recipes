<?php

namespace App\Controllers;

use App\Services\ResponseBuilder;
use App\Services\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class DishController
{
    private \PDO $pdo;
    private ResponseBuilder $responseBuilder;
    private Validator $validator;

    public function __construct(\PDO $pdo, ResponseBuilder $responseBuilder, Validator $validator)
    {
        $this->pdo = $pdo;
        $this->responseBuilder = $responseBuilder;
        $this->validator = $validator;
    }

    public function create(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $requestData = $request->getQueryParams();

        if (!$this->validator->validateRequiredKeys($requestData, [
            'name',
            'alias',
            'quality_id',
        ])) {
            $this->responseBuilder->addError('Не указаны обязательные параметры.');

            return $this->responseBuilder->build($response);
        }

        $data = [
            'name' => $requestData['name'],
            'alias' => $requestData['alias'],
            'quality_id' => intval($requestData['quality_id']),
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
        $query = 'select d.* from dishes d left join qualities q on d.quality_id = q.id order by d.name, q.sort';
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        $result = $stmt->fetchAll();

        $this->responseBuilder->set($result);

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

        $query = 'select d.* from dishes d where d.id = :id';
        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(':id', $requestData['id']);
        $stmt->execute();

        $result = $stmt->fetch();

        $this->responseBuilder->set($result);

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
            $this->responseBuilder->addError('Не указаны обязательные параметры.');

            return $this->responseBuilder->build($response);
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
            $this->responseBuilder->addError('Не указаны обязательные параметры.');

            return $this->responseBuilder->build($response);
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
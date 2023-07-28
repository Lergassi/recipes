<?php

namespace App\Controllers;

use App\Services\DataManager;
use App\Services\ResponseBuilder;
use App\Services\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class BranchController
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

//    public function create(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
//    {
//        /*
//         * Создание ветки.
//         *      Ветка новая или от коммита.
//         * Создание основного рецепта для ветки.
//         */
//
//        return $response;
//    }

//    public function delete(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
//    {
//        return $response;
//    }

//    public function commit(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
//    {
//        $requestData = $request->getQueryParams();
//
//        if (!$this->validator->validateRequiredKeys($requestData, [
//            'id',
//        ])) {
//            $this->responseBuilder->addError('Не указаны обязательные параметры.');
//
//            return $this->responseBuilder->build($response);
//        }
//
//        $data = [
//            'id' => intval($requestData['id']),
//        ];
//
//        $branch = $this->dataManager->findBranch($data['id']);
//        dd($branch);
//
//        return $response;
//    }
}
<?php

namespace App\Controllers;

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

class ReferenceProductController
{
    private PDO $pdo;
    private DataManager $dataManager;
    private ResponseBuilder $responseBuilder;
    private Validator $validator;
    private UniqueConstraintFactory $uniqueConstraintFactory;

    public function __construct(
        PDO                     $pdo,
        ResponseBuilder         $responseBuilder,
        Validator               $validator,
        DataManager             $dataManager,
        UniqueConstraintFactory $uniqueConstraintFactory,
    )
    {
        $this->pdo = $pdo;
        $this->responseBuilder = $responseBuilder;
        $this->validator = $validator;
        $this->dataManager = $dataManager;
        $this->uniqueConstraintFactory = $uniqueConstraintFactory;
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
                    'sort' => new Required([
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
            'sort' => intval($requestData['sort']),
        ];

        if ($this->validator->validate($data, new Collection([
            'fields' => [
                'name' => new Required([
                    new Length(['min' => 1, 'max' => 64]),
                ]),
                'alias' => new Required([
                    new Length(['min' => 1, 'max' => 100]),
                    $this->uniqueConstraintFactory->create([
                        'table' => 'qualities',
                        'column' => 'alias',
                    ]),
                ]),
            ],
            'allowExtraFields' => true,
        ]), $this->responseBuilder)) return $this->responseBuilder->build($response);

        $query = 'insert into reference_products (name, alias, sort) values (:name, :alias, :sort)';

        $stmt = $this->pdo->prepare($query);

        $stmt->bindValue(':name', $data['name']);
        $stmt->bindValue(':alias', $data['alias']);
        $stmt->bindValue(':sort', $data['sort']);

        $stmt->execute();

        $this->responseBuilder->set($this->pdo->lastInsertId());

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

        $referenceProduct = $this->dataManager->findOneReferenceProduct(intval($requestData['id']));

        $this->responseBuilder->set($referenceProduct);

        return $this->responseBuilder->build($response);
    }

    public function all(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $referenceProducts = $this->dataManager->findReferenceProducts();

        $this->responseBuilder->set($referenceProducts);

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
                    'sort' => new Required([
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
            'sort' => intval($requestData['sort']),
        ];

        if ($this->validator->validate($data, new Collection([
            'fields' => [
                'name' => new Required([
                    new Length(['min' => 1, 'max' => 64]),
                ]),
                'alias' => new Required([
                    new Length(['min' => 1, 'max' => 100]),
                    $this->uniqueConstraintFactory->create([
                        'table' => 'qualities',
                        'column' => 'alias',
                        'existsID' => $data['id'],
                    ]),
                ]),
            ],
            'allowExtraFields' => true,
        ]), $this->responseBuilder)) return $this->responseBuilder->build($response);

        $query = 'update reference_products set name = :name, alias = :alias, sort = :sort where id = :id';

        $stmt = $this->pdo->prepare($query);

        $stmt->bindValue(':id', $data['id']);
        $stmt->bindValue(':name', $data['name']);
        $stmt->bindValue(':alias', $data['alias']);
        $stmt->bindValue(':sort', $data['sort']);

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
        //todo: validate foreign keys

        $query = 'delete from reference_products where id = :id';

        $stmt = $this->pdo->prepare($query);

        $stmt->bindValue('id', $ID);

        $stmt->execute();

        $this->responseBuilder->set($stmt->rowCount());
        $response = $this->responseBuilder->build($response);

        return $response;
    }
}
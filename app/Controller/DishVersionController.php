<?php

namespace App\Controller;

use App\DataManager\CommitManager;
use App\DataManager\DishManager;
use App\DataManager\DishVersionManager;
use App\DataManager\QualityManager;
use App\DataManager\RecipeManager;
use App\DataService\DishService;
use App\DataService\DishVersionService;
use App\Exception\AppException;
use App\Factory\ExistsConstraintFactory;
use App\Factory\RecipeFactory;
use App\Factory\UniqueConstraintFactory;
use App\Service\ApiSecurity;
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
    //todo: Возможно слишком много зависимостей.
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
    #[Inject] private DishManager $dishManager;
    #[Inject] private DishService $dishService;
    #[Inject] private DishVersionService $dishVersionService;
    #[Inject] private QualityManager $qualityManager;
    #[Inject] private ApiSecurity $security;

    public function create(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $requestData = $request->getQueryParams();
        if ($this->validator->validate($requestData, [
            //todo: Возможно стоит сделать отдельный валидатор для простого наличия всех полей.
            new Collection([    //todo: Возможно стоит оборачивать стороние решения в классы, как минимум для пометки устаревшего кода. Валидация симфони заменена на respect/validation. А также (или) для единого решения в случае замены инструмента.
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

        //todo: Сделать отдельный объект для работы с данными от клиента.
        $data = [
            'name' => $requestData['name'],
            'alias' => $requestData['alias'],
            'dish_id' => intval($requestData['dish_id']),
            'quality_id' => intval($requestData['quality_id']),
        ];

        $dish = $this->dishManager->findOneByUser($data['dish_id'], $this->security->getUser());
        if (!$dish) throw AppException::entityNotFound();

        $quality = $this->qualityManager->findOne($data['quality_id']);
        if (!$quality) throw AppException::entityNotFound();

        $this->pdo->beginTransaction();

        //todo: Надо придумать алгоритм создания service на основе данных отдельно от manager.
        $dishVersion = $this->dishService->addDishVersion($dish, $data['name'], $data['alias'], $quality);
        $this->dishVersionService->addRecipe($dishVersion,'Оригинальный');

        $this->pdo->commit();

        $this->responseBuilder->set($dishVersion['id']);

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
        ], $this->responseBuilder)) return $this->responseBuilder->build($response);

        $data = [
            'id' => intval($requestData['id']),
        ];

        $dishVersion = $this->dishVersionManager->findOne($data['id'], $this->security->getUser());
        if (!$dishVersion) throw AppException::entityNotFound();

        $dishVersion = $this->build($dishVersion);

        $this->responseBuilder->set($dishVersion);

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

        $dishVersions = $this->dishVersionManager->findByDish(intval($requestData['dish_id']), $this->security->getUser());
        foreach ($dishVersions as &$dishVersion) {
            $dishVersion = $this->build($dishVersion);
        }

        $this->responseBuilder->set($dishVersions);

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

        $dishVersion = $this->dishVersionManager->findOne(intval($requestData['id']), $this->security->getUser());
        if (!$dishVersion) throw AppException::entityNotFound();

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

        $dishVersion = $this->dishVersionManager->findOne($ID, $this->security->getUser());
        if (!$dishVersion) throw AppException::entityNotFound();

        //todo: удаление рецептов и коммитов

        $query = 'delete from dish_versions where id = :id';

        $stmt = $this->pdo->prepare($query);

        $stmt->bindValue('id', $ID);

        $stmt->execute();

        $this->responseBuilder->set($stmt->rowCount());

        return $this->responseBuilder->build($response);
    }

    /**
     * @param array $dishVersion
     * @return array
     */
    public function build(array $dishVersion): array
    {
        $dishVersion['quality'] = $this->qualityManager->findOne($dishVersion['quality_id']);
        unset($dishVersion['quality_id']);
        unset($dishVersion['author_id']);

        return $dishVersion;
    }
}
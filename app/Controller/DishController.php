<?php

namespace App\Controller;

use App\DataManager\DishManager;
use App\DataManager\DishVersionManager;
use App\DataManager\QualityManager;
use App\Exception\AppException;
use App\Factory\DishFactory;
use App\Factory\ExistsConstraintFactory;
use App\Factory\UniqueConstraintFactory;
use App\Service\ApiSecurity;
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

class DishController
{
    #[Inject] private PDO $pdo;
    #[Inject] private DishManager $dishManager;
    #[Inject] private UniqueConstraintFactory $uniqueConstraintFactory;
    #[Inject] private ExistsConstraintFactory $existsConstraintFactory;
    #[Inject] private ResponseBuilder $responseBuilder;
    #[Inject] private Validator $validator;
    #[Inject] private QualityManager $qualityManager;
    #[Inject] private ApiSecurity $security;    //todo: SecurityInterface
    #[Inject] private DishFactory $dishFactory;

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

        $quality = $this->qualityManager->findOne(intval($requestData['quality_id']));
        if (!$quality) return AppException::entityNotFound();

        $dish = $this->dishFactory->create(
            $requestData['name'],
            $requestData['alias'],
            $quality,
            $this->security->getUser(),
        );

        $this->responseBuilder->set($dish['id']);

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

        $dish = $this->dishManager->findOneByUser(intval($requestData['id']), $this->security->getUser());  //todo: Возможно механизм должен быть без getByUser при запросе одной записи, а с проверкой прав доступа после запроса.
        if (!$dish) return $this->responseBuilder
            ->addError('Блюдо не найдено.')
            ->build($response);

        $dish = $this->buildDish($dish);

        $this->responseBuilder->set($dish);

        return $this->responseBuilder->build($response);
    }

    public function all(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $dishes = $this->dishManager->findByUser($this->security->getUser());
        //todo: Времено вынесено из manager.
        //todo: Возможно это нужно убрать на уровень формирования ответа для api. Внутри программы всё равно нет единой логики. Особенно в коммитах и RecipePosition. Но пока тут.
        //todo: SerializeInterface + strategy?
        foreach ($dishes as &$dish) {
            $dish = $this->buildDish($dish);
        }

        $this->responseBuilder->set($dishes);

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

        $dish = $this->dishManager->findOneByUser(intval($requestData['id']), $this->security->getUser());
        if (!$dish) return $this->responseBuilder
            ->addError('Блюдо не найдено.')
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
//                    $this->uniqueConstraintFactory->create([
//                        'table' => 'dishes',
//                        'column' => 'alias',
//                        'existsID' => $data['id'],
//                    ]),
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

        $dish = $this->dishManager->findOneByUser($ID, $this->security->getUser());
        if (!$dish) return $this->responseBuilder
            ->addError('Блюдо не найдено.')
            ->build($response);

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

    private function buildDish(array $dish): array
    {
        $dish['quality'] = $this->qualityManager->findOne($dish['quality_id']);
        unset($dish['quality_id']);
        unset($dish['author_id']);

        return $dish;
    }
}
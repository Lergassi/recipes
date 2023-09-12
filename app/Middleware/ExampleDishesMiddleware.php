<?php

namespace App\Middleware;

use App\DataManager\QualityManager;
use App\DataManager\ReferenceProductManager;
use App\DataService\DishService;
use App\DataService\DishVersionService;
use App\DataService\RecipeService;
use App\Factory\DishFactory;
use App\Service\ApiSecurity;
use App\Type\ReferenceProductID;
use DI\Attribute\Inject;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Random\Engine\Secure;

class ExampleDishesMiddleware implements MiddlewareInterface
{
    #[Inject] private ApiSecurity $security;
    #[Inject] private DishFactory $dishFactory;
    #[Inject] private QualityManager $qualityManager;
    #[Inject] private ReferenceProductManager $referenceProductManager;
    #[Inject] private DishService $dishService;
    #[Inject] private DishVersionService $dishVersionService;
    #[Inject] private RecipeService $recipeService;

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);
//        dump($this->security->getUser());

        $rare = $this->qualityManager->findOneByAlias('rare');
        $epic = $this->qualityManager->findOneByAlias('epic');
        $legendary = $this->qualityManager->findOneByAlias('legendary');

        $data = [
            [
                'name' => 'Плов',
                'alias' => 'pilaf',
                'quality' => 'rare',
                'dishVersions' => [
                    [
                        'name' => 'Плов с курицей',
                        'alias' => 'pilaf_with_chicken',
                        'quality' => 'uncommon',
                        'recipes' => [
                            [
                                'name' => 'Оригинальный',
                                'recipePositions' => [
                                    ['referenceProduct' => ReferenceProductID::Oil->value, 'weight' => 100],

                                    ['referenceProduct' => ReferenceProductID::Water->value, 'weight' => 1000],
                                    ['referenceProduct' => ReferenceProductID::Rice->value, 'weight' => 500],

                                    ['referenceProduct' => ReferenceProductID::Chicken->value, 'weight' => 500],

                                    ['referenceProduct' => ReferenceProductID::Carrot->value, 'weight' => 500],
                                    ['referenceProduct' => ReferenceProductID::Onion->value, 'weight' => 150],
                                    ['referenceProduct' => ReferenceProductID::Garlic->value, 'weight' => 50],

                                    ['referenceProduct' => ReferenceProductID::Salt->value, 'weight' => 30],
                                    ['referenceProduct' => ReferenceProductID::Cumin->value, 'weight' => 10],
                                    ['referenceProduct' => ReferenceProductID::Black_pepper->value, 'weight' => 5],
                                ],
                            ],
                            [
                                'name' => 'Больше специй',
                                'recipePositions' => [
                                    ['referenceProduct' => ReferenceProductID::Oil->value, 'weight' => 100],

                                    ['referenceProduct' => ReferenceProductID::Water->value, 'weight' => 1000],
                                    ['referenceProduct' => ReferenceProductID::Rice->value, 'weight' => 500],

                                    ['referenceProduct' => ReferenceProductID::Chicken->value, 'weight' => 500],

                                    ['referenceProduct' => ReferenceProductID::Carrot->value, 'weight' => 500],
                                    ['referenceProduct' => ReferenceProductID::Onion->value, 'weight' => 150],
                                    ['referenceProduct' => ReferenceProductID::Garlic->value, 'weight' => 100],

                                    ['referenceProduct' => ReferenceProductID::Salt->value, 'weight' => 30],
                                    ['referenceProduct' => ReferenceProductID::Cumin->value, 'weight' => 15],
                                    ['referenceProduct' => ReferenceProductID::Coriander->value, 'weight' => 5],
                                    ['referenceProduct' => ReferenceProductID::Garlic_powder->value, 'weight' => 10],
                                    ['referenceProduct' => ReferenceProductID::Black_pepper->value, 'weight' => 10],
                                ],
                            ],
                            [
                                'name' => 'Больше специй + острый',
                                'recipePositions' => [
                                    ['referenceProduct' => ReferenceProductID::Oil->value, 'weight' => 100],

                                    ['referenceProduct' => ReferenceProductID::Water->value, 'weight' => 1000],
                                    ['referenceProduct' => ReferenceProductID::Rice->value, 'weight' => 500],

                                    ['referenceProduct' => ReferenceProductID::Chicken->value, 'weight' => 500],

                                    ['referenceProduct' => ReferenceProductID::Carrot->value, 'weight' => 500],
                                    ['referenceProduct' => ReferenceProductID::Onion->value, 'weight' => 150],
                                    ['referenceProduct' => ReferenceProductID::Garlic->value, 'weight' => 200],

                                    ['referenceProduct' => ReferenceProductID::Salt->value, 'weight' => 30],
                                    ['referenceProduct' => ReferenceProductID::Cumin->value, 'weight' => 15],
                                    ['referenceProduct' => ReferenceProductID::Coriander->value, 'weight' => 5],
                                    ['referenceProduct' => ReferenceProductID::Garlic_powder->value, 'weight' => 20],
                                    ['referenceProduct' => ReferenceProductID::Black_pepper->value, 'weight' => 20],
                                    ['referenceProduct' => ReferenceProductID::Chili_pepper->value, 'weight' => 20],
                                ],
                            ],
                            [
                                'name' => 'Больше мяса',
                                'recipePositions' => [
                                    ['referenceProduct' => ReferenceProductID::Oil->value, 'weight' => 100],

                                    ['referenceProduct' => ReferenceProductID::Water->value, 'weight' => 1000],
                                    ['referenceProduct' => ReferenceProductID::Rice->value, 'weight' => 500],

                                    ['referenceProduct' => ReferenceProductID::Chicken->value, 'weight' => 700],

                                    ['referenceProduct' => ReferenceProductID::Carrot->value, 'weight' => 500],
                                    ['referenceProduct' => ReferenceProductID::Onion->value, 'weight' => 150],
                                    ['referenceProduct' => ReferenceProductID::Garlic->value, 'weight' => 50],

                                    ['referenceProduct' => ReferenceProductID::Salt->value, 'weight' => 30],
                                    ['referenceProduct' => ReferenceProductID::Cumin->value, 'weight' => 10],
                                    ['referenceProduct' => ReferenceProductID::Black_pepper->value, 'weight' => 5],
                                ],
                            ],
                        ],
                    ],
                    [
                        'name' => 'Плов со свининой',
                        'alias' => 'pilaf_with_pork',
                        'quality' => 'rare',
                        'recipes' => [
                            [
                                'name' => 'Оригинальный',
                                'recipePositions' => [
                                    ['referenceProduct' => ReferenceProductID::Oil->value, 'weight' => 100],

                                    ['referenceProduct' => ReferenceProductID::Water->value, 'weight' => 1000],
                                    ['referenceProduct' => ReferenceProductID::Rice->value, 'weight' => 500],

                                    ['referenceProduct' => ReferenceProductID::Pork->value, 'weight' => 500],

                                    ['referenceProduct' => ReferenceProductID::Carrot->value, 'weight' => 500],
                                    ['referenceProduct' => ReferenceProductID::Onion->value, 'weight' => 150],
                                    ['referenceProduct' => ReferenceProductID::Garlic->value, 'weight' => 50],

                                    ['referenceProduct' => ReferenceProductID::Salt->value, 'weight' => 30],
                                    ['referenceProduct' => ReferenceProductID::Cumin->value, 'weight' => 10],
                                    ['referenceProduct' => ReferenceProductID::Black_pepper->value, 'weight' => 5],
                                ],
                            ],
                        ],
                    ],
                    [
                        'name' => 'Плов с говядиной',
                        'alias' => 'pilaf_with_beef',
                        'quality' => 'epic',
                        'recipes' => [
                            [
                                'name' => 'Оригинальный',
                                'recipePositions' => [
                                    ['referenceProduct' => ReferenceProductID::Oil->value, 'weight' => 100],

                                    ['referenceProduct' => ReferenceProductID::Water->value, 'weight' => 1000],
                                    ['referenceProduct' => ReferenceProductID::Rice->value, 'weight' => 500],

                                    ['referenceProduct' => ReferenceProductID::Beef->value, 'weight' => 500],

                                    ['referenceProduct' => ReferenceProductID::Carrot->value, 'weight' => 500],
                                    ['referenceProduct' => ReferenceProductID::Onion->value, 'weight' => 150],
                                    ['referenceProduct' => ReferenceProductID::Garlic->value, 'weight' => 100],

                                    ['referenceProduct' => ReferenceProductID::Salt->value, 'weight' => 30],
                                    ['referenceProduct' => ReferenceProductID::Cumin->value, 'weight' => 15],
                                    ['referenceProduct' => ReferenceProductID::Coriander->value, 'weight' => 5],
                                    ['referenceProduct' => ReferenceProductID::Garlic_powder->value, 'weight' => 10],
                                    ['referenceProduct' => ReferenceProductID::Black_pepper->value, 'weight' => 10],
                                ],
                            ],
                        ],
                    ],
                    [
                        'name' => 'Плов с бараниной',
                        'alias' => 'pilaf_with_mutton',
                        'quality' => 'legendary',
                        'recipes' => [
                            [
                                'name' => 'Оригинальный',
                                'recipePositions' => [
                                    ['referenceProduct' => ReferenceProductID::Oil->value, 'weight' => 100],

                                    ['referenceProduct' => ReferenceProductID::Water->value, 'weight' => 1000],
                                    ['referenceProduct' => ReferenceProductID::Rice->value, 'weight' => 500],

                                    ['referenceProduct' => ReferenceProductID::Mutton->value, 'weight' => 500],

                                    ['referenceProduct' => ReferenceProductID::Carrot->value, 'weight' => 500],
                                    ['referenceProduct' => ReferenceProductID::Onion->value, 'weight' => 150],
                                    ['referenceProduct' => ReferenceProductID::Garlic->value, 'weight' => 100],

                                    ['referenceProduct' => ReferenceProductID::Salt->value, 'weight' => 30],
                                    ['referenceProduct' => ReferenceProductID::Cumin->value, 'weight' => 15],
                                    ['referenceProduct' => ReferenceProductID::Coriander->value, 'weight' => 5],
                                    ['referenceProduct' => ReferenceProductID::Garlic_powder->value, 'weight' => 10],
                                    ['referenceProduct' => ReferenceProductID::Black_pepper->value, 'weight' => 10],
                                ],
                            ],
                        ],
                    ],
                ],//end dish versions
            ],//end плов
        ];

        $user = $this->security->getUser();
        foreach ($data as $dishDatum) {
            $dish = $this->dishFactory->create(
                $dishDatum['name'],
                $dishDatum['alias'],
                $this->qualityManager->findOneByAlias($dishDatum['quality']),
                $user,
            );

            foreach ($dishDatum['dishVersions'] as $dishVersionDatum) {
                /*
                 * Варианты:
                 * addDishVersion(...): object
                 *
                 * object->getArray();
                 * object->build(): DishVersion; Заменяет %entity%_id на другие объекты.
                 * object->buildArray(); Это можно оставить на момент необходимости, например при отправки данных на клиент.
                 * object->service(): DishVersionService или return будет сервис, который будет отвечать за другие действия;
                 */
                $dishVersion = $this->dishService->addDishVersion(
                    $dish,
                    $dishVersionDatum['name'],
                    $dishVersionDatum['alias'],
                    $this->qualityManager->findOneByAlias($dishVersionDatum['quality']),
                );

                foreach ($dishVersionDatum['recipes'] as $recipeDatum) {
                    $recipe = $this->dishVersionService->addRecipe($dishVersion, $recipeDatum['name']);
                    foreach ($recipeDatum['recipePositions'] as $recipePositionDatum) {
                        $this->recipeService->addProduct(
                            $recipe['id'],
                            $this->referenceProductManager->findOneByAlias($recipePositionDatum['referenceProduct'])['id'],
                            $recipePositionDatum['weight'],
                        );
                    }
                    $this->recipeService->commit($recipe['id']);
                }
            }
        }

        return $response;
    }
}
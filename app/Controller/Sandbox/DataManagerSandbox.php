<?php

namespace App\Controller\Sandbox;

use App\DataManager\DishManager;
use App\DataManager\QualityManager;
use App\DataManager\ReferenceProductManager;
use App\DataManager\UserManager;
use App\Factory\DishFactory;
use App\Service\DataManager;
use App\Test\Foo;
use DI\Attribute\Inject;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class DataManagerSandbox extends AbstractSandboxController
{
    #[Inject] private DataManager $dataManager;
    #[Inject] private QualityManager $qualityManager;
    #[Inject] private ReferenceProductManager $referenceProductManager;
    #[Inject] private DishManager $dishManager;
    #[Inject] private UserManager $userManager;
    #[Inject] private DishFactory $dishFactory;

    public function run(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
//        $this->exists();
//        $this->findHead();
//        $this->separateDataManager();
//        $this->buildArray();
//        $this->apiBuild_getStarted();
        $this->dishFactory();

        return $response;
    }

    private function exists()
    {
//        $alias = 'pilaf';
        $alias = 'pilaf_01';

        dump($this->dataManager->count('dishes', 'alias', $alias));
    }

    private function findHead()
    {
        $recipeID = 1;
        dump($this->dataManager->findHeadRecipeCommit(1));
        dump($this->dataManager->findHeadRecipeCommit(6));
    }

    private function buildArray()
    {
//        $dish = $thi
    }

    private function separateDataManager()
    {
//        dump($this->qualityManager->findOne(1));
//        dump($this->qualityManager->findOne(42));
//        dump($this->qualityManager->find());

//        dump($this->referenceProductManager->findOne(1));
//        dump($this->referenceProductManager->findOne(420));
//        dump($this->referenceProductManager->find());

//        dump($this->dishManager->findOne(1));
//        dump($this->dishManager->findOne(42));
//        dump($this->dishManager->find());
    }

    private function apiBuild_getStarted()
    {
        $dish = $this->dishManager->findOne(1);

        $dish['quality'] = $this->qualityManager->findOne($dish['quality_id']);
        unset($dish['quality_id']);

        dump($dish);
        dump($this->dishRelationBuilder->buildOne($this->dishManager->findOne(1)));

        $dishesData = $this->dishManager->find();
        $dishes = [];
        $count = count($dishesData);
        for ($i = 0; $i < $count; $i++) {
            $dishes[$i] = $dishesData[$i];
            $dishes[$i]['quality'] = $this->qualityManager->findOne($dishes[$i]['quality_id']);
            unset($dishes[$i]['quality_id']);
        }
        dump($dishesData);
        dump($dishes);

        dump($this->dishRelationBuilder->build($this->dishManager->find()));

//        $a = [1,2,3,4,5, new Foo(42)];
//        $b = $a;
//        unset($a[2]);
//        unset($a[5]);
//        dump($a);
//        dump($b);
    }

    private function dishFactory()
    {
        $user = $this->userManager->findOneEntityByEmail('user01@site.ru');
        $quality = $this->qualityManager->findOneByAlias('common');

        dump($this->dishFactory->create(
            'Плов',
            'plov',
            $quality,
            $user,
        ));
    }
}
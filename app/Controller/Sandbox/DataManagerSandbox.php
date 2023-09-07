<?php

namespace App\Controller\Sandbox;

use App\DataManager\DishManager;
use App\DataManager\QualityManager;
use App\DataManager\ReferenceProductManager;
use App\Service\DataManager;
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

    public function run(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
//        $this->exists();
//        $this->findHead();
        $this->separateDataManager();
//        $this->buildArray();

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
}
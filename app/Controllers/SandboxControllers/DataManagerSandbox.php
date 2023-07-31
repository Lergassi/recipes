<?php

namespace App\Controllers\SandboxControllers;

use App\Services\DataManager;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class DataManagerSandbox extends AbstractSandboxController
{
    private DataManager $dataManager;

    public function __construct(ContainerInterface $container, DataManager $dataManager)
    {
        parent::__construct($container);

        $this->dataManager = $dataManager;
    }

    public function run(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
//        $this->exists();
        $this->findHead();

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
}
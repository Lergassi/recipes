<?php

namespace App\Controllers\SandboxControllers;

use App\Services\AliasGenerator;
use Behat\Transliterator\Transliterator;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class MainSandboxController
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function main(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
//        $this->_devMysqlGetStarted();
//        $this->_devStrval();
//        $this->_devRequestQueryParams($request);
        $this->_devAliasGenerator($request);

        return $response;
    }

    private function _devMysqlGetStarted()
    {
        dump($this->container->get(\PDO::class));
    }

    private function _devStrval()
    {
        dump([
            strval(-1),
            strval(0),
            strval(1),
            strval(null),
//            strval([]),
        ]);
    }

    private function _devRequestQueryParams(ServerRequestInterface $request)
    {
        dump($request->getQueryParams());
    }

    private function _devAliasGenerator(ServerRequestInterface $request)
    {
//        dump(Transliterator::transliterate('Привет, Мир!', '_'));
        dump(Transliterator::transliterate('борщ', '_'));

//        $name = 'pilaf';
//        $name = 'blini';
        $name = 'Блины';

        /** @var AliasGenerator $aliasGenerator */
        $aliasGenerator = $this->container->get(AliasGenerator::class);
//        dump($aliasGenerator->generate($name));
//        dump($aliasGenerator->generate($name, 1));
//        dump($aliasGenerator->generate($name, 42));
//        dump($aliasGenerator->generate($name, 142));
//        dump($aliasGenerator->generate($name, 0));
//        dump($aliasGenerator->generate($name, 5));
//        dump($aliasGenerator->generate($name, -5));

//        dump($aliasGenerator->generateByRecordsCount($name, 'dishes'));
    }
}
<?php

namespace App\Controllers\SandboxControllers;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class MainSandboxController
{
    private ContainerInterface $_container;

    public function __construct(ContainerInterface $container)
    {
        $this->_container = $container;
    }

    public function main(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $this->_devMysqlGetStarted();

        return $response;
    }

    private function _devMysqlGetStarted()
    {
        dump($this->_container->get(\PDO::class));
    }
}
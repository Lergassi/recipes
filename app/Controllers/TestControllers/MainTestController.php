<?php

namespace App\Controllers\TestControllers;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class MainTestController
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function testDump(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        dump('Hello, World!');
        dd('Hello, World!');

        return $response;
    }

    public function testContainer(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        dump([
            $this->container instanceof ContainerInterface,
            'container was injected',
        ]);

        return $response;
    }
}
<?php

namespace App\Controllers\SandboxControllers;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

abstract class AbstractSandboxController
{
    public abstract function run(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface;

    private ContainerInterface $_container;

    /**
     * @return ContainerInterface
     */
    public function getContainer(): ContainerInterface
    {
        return $this->_container;
    }

    public function __construct(ContainerInterface $container)
    {
        $this->_container = $container;
    }

    public function main(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->run($request, $response);
    }
}
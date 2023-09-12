<?php

namespace App\Controller\Sandbox;

use App\Exception\AppException;
use App\Service\ApiSecurity;
use DI\Attribute\Inject;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class MiddlewareSandboxController extends AbstractSandboxController
{
    #[Inject] private ApiSecurity $security;

    public function run(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $response;
    }

    public function loginByApiKey(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        dump($this->security);
//        dump(__METHOD__);

//        dump(strval($response->getBody()));
        $response->getBody()->write(__METHOD__);
//        dump(strval($response->getBody()));

//        throw AppException::accessDenied();

        return $response;
    }
}
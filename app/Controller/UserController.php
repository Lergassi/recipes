<?php

namespace App\Controller;

use App\Service\ApiSecurity;
use App\Service\ResponseBuilder;
use DI\Attribute\Inject;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class UserController
{
    #[Inject] private ApiSecurity $security;
    #[Inject] private ResponseBuilder $responseBuilder;

    public function info(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $this->responseBuilder->set($this->security->getUser())->build($response);

        return $response;
    }
}
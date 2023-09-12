<?php

namespace App\Middleware;

use App\Exception\AppException;
use App\Service\ApiSecurity;
use DI\Attribute\Inject;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class OnlyAuthMiddleware implements MiddlewareInterface
{
    #[Inject] private ApiSecurity $security;

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (!$this->security->isAuth()) throw AppException::accessDenied();

        return $handler->handle($request);
    }
}
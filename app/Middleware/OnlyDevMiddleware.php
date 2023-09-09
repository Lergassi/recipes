<?php

namespace App\Middleware;

use App\Exception\AppException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class OnlyDevMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($_ENV['APP_ENV'] !== 'dev') throw new AppException();

        return $handler->handle($request);
    }
}
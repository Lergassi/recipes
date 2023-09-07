<?php

namespace App\Middleware;

use App\DataManager\UserManager;
use App\Service\ApiSecurity;
use DI\Attribute\Inject;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Respect\Validation\Validator;

class TryLoginByApiKeyMiddleware implements MiddlewareInterface
{
    #[Inject] private UserManager $userManager;
    #[Inject] private ApiSecurity $security;

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $requestData = $request->getQueryParams();
        if (Validator::key('api_key')->validate($requestData)) {
//        if (isset($request['api_key'])) {
            $user = $this->userManager->findOneEntityByApiKey($requestData['api_key']);
            if ($user) {
                $this->security->login($user);
            }
        }

        return $handler->handle($request);
    }
}
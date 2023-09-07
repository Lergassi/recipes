<?php

namespace App\Controller\Security;

use App\DataManager\UserManager;
use App\Exception\AppException;
use App\Service\ApiKeyGenerator;
use App\Service\ApiSecurity;
use App\Service\DataService\UserService;
use App\Service\ResponseBuilder;
use DI\Attribute\Inject;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Respect\Validation\Validator;

class AuthController
{
    #[Inject] private UserManager $userManager;
    #[Inject] private UserService $userService;
    #[Inject] private ResponseBuilder $responseBuilder;
    #[Inject] private ApiSecurity $security;
    #[Inject] private ApiKeyGenerator $apiKeyGenerator;

    public function generateApiKey(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = $request->getQueryParams(); //todo: возможно стоит привести к одному формату get/post если данных нету. Сейчас в get массив, в post null.
        Validator::keySet(
            Validator::key('email', Validator::notBlank()),
            Validator::key('password', Validator::notBlank()),
        )->assert($data);

        $user = $this->userManager->findOneByEmailEntity($data['email']);
        if (!$user) throw AppException::userNotFound();
        if (!$user->verifyPassword($data['password'])) throw AppException::userNotFound();

        $apiKey = $this->apiKeyGenerator->generate();
        $this->userService->setApiKey($user, $apiKey);
        $this->security->login($user);

        return $this->responseBuilder->set($apiKey)->build($response);
    }

//    public function logout(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
//    {
//        $this->security->logout();
//
//        return $response->withStatus(StatusCodeInterface::STATUS_SEE_OTHER)->withHeader(Header::LOCATION, '/');
//    }
}
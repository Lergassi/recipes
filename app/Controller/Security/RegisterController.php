<?php

namespace App\Controller\Security;

use App\DataManager\UserManager;
use App\DataService\UserService;
use App\Factory\UserFactory;
use App\Service\ApiKeyGenerator;
use App\Service\ApiSecurity;
use App\Service\ResponseBuilder;
use App\Service\Security;
use App\Type\UserGroupID;
use DI\Attribute\Inject;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Respect\Validation\Validator;

class RegisterController
{
    #[Inject] private \PDO $pdo;
    #[Inject] private UserFactory $userFactory;
    #[Inject] private UserService $userService;
    #[Inject] private ResponseBuilder $responseBuilder;
    #[Inject] private ApiKeyGenerator $apiKeyGenerator;
    #[Inject] private ApiSecurity $security;

    public function register(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $requestData = $request->getQueryParams();
        Validator::keySet(
            Validator::key('email', Validator::notBlank()), //todo: В модуле должен быть выбор: почта и/или логин.
            Validator::key('password', Validator::notBlank()),
//            Validator::key('api_key', null, false),
        )->assert($requestData);

        $this->pdo->beginTransaction();

        $user = $this->userFactory->create(trim($requestData['email']), $requestData['password'], [
            UserGroupID::User->value,
        ]);

        $apiKey = $this->apiKeyGenerator->generate();
        $this->userService->setApiKey($user, $apiKey);
        $this->security->login($user);

        $this->pdo->commit();

        return $this->responseBuilder->set($apiKey)->build($response);
    }
}
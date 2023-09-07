<?php

namespace App\Controller\Sandbox;

use App\DataManager\UserManager;
use App\Factory\ExistsConstraintFactory;
use App\Factory\UniqueConstraintFactory;
use App\Service\AliasGenerator;
use App\Service\ApiKeyGenerator;
use App\Service\ApiSecurity;
use App\Service\Validation\Validator;
use Behat\Transliterator\Transliterator;
use DI\Attribute\Inject;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Required;
use Symfony\Component\Validator\Validation;

class MainSandboxController
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function main(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
//        $this->_devMysqlGetStarted();
//        $this->_devStrval();
//        $this->_devRequestQueryParams($request);
//        $this->_devAliasGenerator($request);
//        $this->validationBySymfony($request);
//        $this->devUniqueConstraint($request);
//        $this->devApiKeyAuth();
        $this->authByApiKey();

        return $response;
    }

    private function _devMysqlGetStarted()
    {
        dump($this->container->get(\PDO::class));
    }

    private function _devStrval()
    {
        dump([
            strval(-1),
            strval(0),
            strval(1),
            strval(null),
//            strval([]),
        ]);
    }

    private function _devRequestQueryParams(ServerRequestInterface $request)
    {
        dump($request->getQueryParams());
    }

    private function _devAliasGenerator(ServerRequestInterface $request)
    {
//        dump(Transliterator::transliterate('Привет, Мир!', '_'));
        dump(Transliterator::transliterate('борщ', '_'));

//        $name = 'pilaf';
//        $name = 'blini';
        $name = 'Блины';

        /** @var AliasGenerator $aliasGenerator */
        $aliasGenerator = $this->container->get(AliasGenerator::class);
//        dump($aliasGenerator->generate($name));
//        dump($aliasGenerator->generate($name, 1));
//        dump($aliasGenerator->generate($name, 42));
//        dump($aliasGenerator->generate($name, 142));
//        dump($aliasGenerator->generate($name, 0));
//        dump($aliasGenerator->generate($name, 5));
//        dump($aliasGenerator->generate($name, -5));

//        dump($aliasGenerator->generateByRecordsCount($name, 'dishes'));
    }

    private function validationBySymfony(ServerRequestInterface $request)
    {
        $data = [
//            'name' => 'this is name',
//            'name' => '',
//            'name' => ' ',
            'name' => null,
//            'name' => 0,
            'alias' => 'asds',
            'sort' => 500,
        ];

        $symfonyValidator = Validation::createValidator();
        $violations = $symfonyValidator->validate($data['name'], [
            new Length(['min' => 1, 'max' => 64]),
            new NotBlank(),
        ]);
//        dump($violations);

        $errors = [];
        $validator = new Validator();
//        $validator->validate($data['name'], [
//            new Length(['min' => 0, 'max' => 64]),
//            new NotBlank(),
//        ], $errors);
//        dump($errors);

//        dump($symfonyValidator->validate($data, [
//            new Collection([
//                'fields' => new Required(),
//            ]),
//        ]));
        dump($symfonyValidator->validate($data, [
            new Collection([
                'fields' => [
                    'name' => new Required(),
                    'sort' => new Required(),
                    'sort1' => new Required(),
                ],
                'allowExtraFields' => true,
            ]),
        ]));
    }

    private function devUniqueConstraint(ServerRequestInterface $request)
    {
        /** @var UniqueConstraintFactory $uniqueConstraintFactory */
        $uniqueConstraintFactory = $this->container->get(UniqueConstraintFactory::class);
        /** @var ExistsConstraintFactory $existsConstraintFactory */
        $existsConstraintFactory = $this->container->get(ExistsConstraintFactory::class);

//        dump(new UniqueConstraint([
//            'table' => 'qualities',
//            'field' => 'alias',
////                'value' => 'common',
//        ]));
//        dump($this->container->get(UniqueConstraint::class));
        $symfonyValidator = Validation::createValidator();
//        $value = 'this is name';
        $value = 'uncommon';
        dump($symfonyValidator->validate($value, [
            $uniqueConstraintFactory->create([
                'table' => 'qualities',
                'column' => 'alias',
//                'existsID' => 2,
                'existsID' => 1,
            ]),
//            $this->container->get(UniqueConstraint::class),
//            new UniqueConstraint([
//                'table' => 'qualities',
//                'field' => 'alias',
////                'value' => 'common',
//            ]),
        ]));

//        $collection = [
//            'alias' => 'uncommon',
////            'alias' => '',
//        ];
//        dump($symfonyValidator->validate($collection, new Collection([
//            'fields' => [
//                'alias' => new Required([
//                    $uniqueConstraintFactory->create([
//                        'table' => 'qualities',
//                        'column' => 'alias',
//                    ]),
//                ]),
//            ],
//        ])));

//        $value = 1;
        $value = 42;
        dump($symfonyValidator->validate($value, [
            $existsConstraintFactory->create([
                'table' => 'qualities',
//                'column' => 'alias',
//                'existsID' => 2,
//                'existsID' => 1,
            ]),
//            $this->container->get(UniqueConstraint::class),
//            new UniqueConstraint([
//                'table' => 'qualities',
//                'field' => 'alias',
////                'value' => 'common',
//            ]),
        ]));
    }

    #[Inject] private ApiSecurity $security;
    #[Inject] private ApiKeyGenerator $apiKeyGenerator;
    #[Inject] private UserManager $userManager;

    private function devApiKeyAuth()
    {
        $user = $this->userManager->findOneByEmailEntity('user01@site.ru');

        $apiKey = $this->security->login($user);
        dump($apiKey);
        dump($this->security);
//        dump(random_bytes(256));
//        dump($this->apiKeyGenerator->generate());
//        dump(Uuid::uuid4());
    }

    private function authByApiKey()
    {
        $apiKey = '8bb5fc75-ee69-40b9-bca0-4a31c46acd07';
//        $apiKey = '';
//        $apiKey = 42;

        $user = $this->userManager->findOneEntityByApiKey($apiKey);
        dump($user);

        $this->security->login($user);
    }
}
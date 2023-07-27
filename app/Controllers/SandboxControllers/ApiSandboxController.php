<?php

namespace App\Controllers\SandboxControllers;

use App\Services\ResponseBuilder;
use App\Services\Serializer;
use http\Header;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Response;

class ApiSandboxController extends AbstractSandboxController
{
    public function run(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
//        $this->_getStarted();
        $response = $this->_testJsonGetStarted($request, $response);
//        $response->withHeader('Content-Type', 'application/json');
//        $r1 = $response->withAddedHeader('Content-Type', 'application/json');
//        dump($response);
//        dump($r1);

//        $r2 = new Response();
//        $r2->withAddedHeader('Content-Type', 'application/json');
//        $r3 = $r2->withHeader('Content-Type', 'application/json');

//        \header('Content-Type', 'application/json');

//        $newResponse = $response->withHeader('Content-Type', 'application/json');

        return $response;
//        return $newResponse;
//        return $r1;
//        return $r2;
//        return $r3;
    }

    private function _getStarted()
    {

    }

    private function _testJsonGetStarted(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $arr = [
            -1,
            0,
            1,
            1.42,
            42,
            '',
            ' ',
            null,
            [],
            [1,2,3,4,5],
            ['Hello, World!'],
            ['Привет, Мир!'],
            '01' => 1,
            'test' => 'this is test',
        ];



//        $json = json_encode($arr, JSON_UNESCAPED_UNICODE);
        $jsonService = new Serializer();
        /** @var ResponseBuilder $responseBuilder */
        $responseBuilder = $this->getContainer()->get(ResponseBuilder::class);
//        $json = json_encode($arr, JSON_UNESCAPED_UNICODE);
//        $json = $jsonService->encode($arr);

        $responseBuilder->set($arr);
//        $responseBuilder->addError('this is error');

//        $response = $response->withHeader('Content-Type', 'application/json');
        $response = $responseBuilder->build($response);

//        $response->getBody()->write($json);

        return $response;
    }
}
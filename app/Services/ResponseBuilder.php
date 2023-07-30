<?php

namespace App\Services;

use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Psr7\Headers;
use Slim\Psr7\Response;

class ResponseBuilder
{
    private mixed $content;
    private array $errors;

    private Serializer $serializer;

    public function __construct(Serializer $serializer)
    {
        $this->content = '';
        $this->errors = [];
        $this->serializer = $serializer;
    }

    public function set(mixed $content): ResponseBuilder
    {
        $this->content = $content;

        return $this;
    }

    public function addError(string $error): ResponseBuilder
    {
        $this->errors[] = $error;

        return $this;
    }

    public function build(ResponseInterface $response = null): ResponseInterface
//    public function build(ResponseInterface $response): ResponseInterface
    {
        $headers = new Headers();
        $headers->addHeader('Content-type', 'application/json');

        if (!$response) {
            $response = new Response(StatusCodeInterface::STATUS_OK, $headers);
        } else {
            $response = $response->withHeader('Content-type', 'application/json');
        }

        $response = $response->withHeader('Content-type', 'application/json');

        if (!count($this->errors)) {
            $responseBody = [
                'response' => $this->content,
            ];
        } else {
            $responseBody = [
                'error' => $this->errors,
            ];
        }

        $response->getBody()->write($this->serializer->encode($responseBody));

        return $response;
    }
}
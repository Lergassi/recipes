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

    public function set(mixed $content): void
    {
        $this->content = $content;
    }

    public function addError(string $error): int
    {
        $this->errors[] = $error;

        return count($this->errors);
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
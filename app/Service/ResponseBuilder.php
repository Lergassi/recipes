<?php

namespace App\Service;

use DI\Attribute\Inject;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Psr7\Headers;
use Slim\Psr7\Response;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ResponseBuilder
{
    private mixed $content;
    private array $errors;

    #[Inject] private Serializer $serializer;

    public function __construct()
    {
        $this->content = '';
        $this->errors = [];
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

    public function addViolations(ConstraintViolationListInterface $violationList): ResponseBuilder
    {
        foreach ($violationList as $item) {
            $this->addError($item->getMessage());
        }

        return $this;
    }

    public function build(ResponseInterface $response = null): ResponseInterface
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

    public function hasErrors(): bool
    {
        return count($this->errors);
    }
}
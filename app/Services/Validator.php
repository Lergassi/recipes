<?php

namespace App\Services;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class Validator
{
    private ValidatorInterface $validator;

    public function __construct()
    {
        $this->validator = Validation::createValidator();
    }

    //todo $errorInterface не builder, а интерфейс сбора ошибок с методом add/addError. Из-за return $this в builder единый интерфейс пока сделать не получается.
    public function validate(mixed $value, Constraint|array $constraints , ResponseBuilder $errorInterface): int
    {
        $violations = $this->validator->validate($value, $constraints);
        if ($violations->count()) {
            $errorInterface->addViolations($violations);
        }

        return $violations->count();
    }
}
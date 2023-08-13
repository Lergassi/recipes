<?php

namespace App\Services;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class UniqueConstraintValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint)
    {
        if (!$constraint instanceof UniqueConstraint) {
            throw new UnexpectedValueException($constraint, UniqueConstraint::class);
        }

//        $query = sprintf('select count(*) from %s where %s = :%s');
//        dump($query);

        dump($constraint);
        dd($value);
    }
}
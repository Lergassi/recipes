<?php

namespace App\Service\Validation;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class ExistsConstraintValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint)
    {
        if (!$constraint instanceof ExistsConstraint) {
            throw new UnexpectedValueException($constraint, ExistsConstraint::class);
        }

        $query = sprintf('select count(*) as count from %s where id = :id',
            $constraint->table,
        );
        $stmt = $constraint->pdo->prepare($query);

        $stmt->bindValue(':id', $value);

        $stmt->execute();

        $result = $stmt->fetch();

        if ($result['count'] === 0) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }
}
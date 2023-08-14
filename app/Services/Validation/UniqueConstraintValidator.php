<?php

namespace App\Services\Validation;

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

        $existsIDQueryPart = $constraint->existsID ? ' and id <> :id' : '';
        $query = sprintf('select count(*) as count from %s where %s = :%s%s',
            $constraint->table,
            $constraint->column,
            $constraint->column,
            $existsIDQueryPart
        );
        $stmt = $constraint->pdo->prepare($query);

        $stmt->bindValue(':' . $constraint->column, $value);
        if ($constraint->existsID) $stmt->bindValue(':id', $constraint->existsID);

        $stmt->execute();

        $result = $stmt->fetch();

        if ($result['count'] >= 1) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }
}
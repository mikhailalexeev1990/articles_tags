<?php

declare(strict_types=1);

namespace App\Validator;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class EntityExistsConstraintValidator extends ConstraintValidator
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @param EntityExistsConstraint $constraint
     */
    public function validate(mixed $value, Constraint $constraint): void
    {
        if ($value === null) {
            return;
        }

        if (!$constraint instanceof EntityExistsConstraint) {
            throw new UnexpectedTypeException($constraint, EntityExistsConstraint::class);
        }

        $entity = $this->entityManager->find($constraint->class, $value);

        if ($entity === null) {
            $this->context->buildViolation('Entity wasn\'t found.')->addViolation();
        }
    }
}

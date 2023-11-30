<?php

declare(strict_types=1);

namespace App\Exception;

use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class InvalidDataException extends Exception implements InvalidDataExceptionInterface
{
    public function __construct(
        private readonly ConstraintViolationListInterface $violationList,
        private readonly int $status = Response::HTTP_UNPROCESSABLE_ENTITY,
    ) {
        parent::__construct('Invalid data');
    }

    public function getErrorMessages(): array
    {
        $errors = [];
        /** @var ConstraintViolationInterface $violation */
        foreach ($this->violationList as $index => $violation) {
            $name = preg_replace("/^(.*?\[(.*?)\])/", '$2', $violation->getPropertyPath());
            $name = $name ?: $index;
            $errors[$name]['name'] = $name;
            $errors[$name]['messages'][] = $violation->getMessage();
            $errors[$name]['children'] = [];
        }

        return array_values($errors);
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function getViolations(): ConstraintViolationListInterface
    {
        return $this->violationList;
    }
}

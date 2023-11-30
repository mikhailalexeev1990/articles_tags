<?php

namespace App\Tests\Unit;

use App\Exception\InvalidDataException;
use PHPUnit\Framework\TestCase;

abstract class UnitTestCase extends TestCase
{
    protected function assertContainsValidationError(string $propertyPath, InvalidDataException $exception): void
    {
        $violations = $exception->getViolations();
        foreach ($violations as $violation) {
            if ($violation->getPropertyPath() === $propertyPath) {
                return;
            }
        }

        $this->fail(sprintf('Expected validation error "%s" not found in the exception.', $propertyPath));
    }
}

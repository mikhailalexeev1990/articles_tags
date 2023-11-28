<?php

namespace App\Exception;

interface InvalidDataExceptionInterface
{
    public function getErrorMessages(): array;

    public function getStatus(): int;
}

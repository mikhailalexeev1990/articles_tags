<?php

declare(strict_types=1);

namespace App\Exception;

use Exception;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormErrorIterator;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;

class InvalidFormDataException extends Exception implements InvalidDataExceptionInterface
{
    public function __construct(
        private readonly FormInterface $form,
        private readonly int $status = Response::HTTP_UNPROCESSABLE_ENTITY,
    ) {
        parent::__construct('Invalid form data');
    }

    public function getErrorMessages(): array
    {
        $errors = $this->form->getErrors(true, false);

        return $this->getErrorsRecursive($errors);
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    private function getErrorsRecursive(FormErrorIterator $errors): array
    {
        $errorMessages = [];

        foreach ($errors as $error) {
            if ($error instanceof FormErrorIterator) {
                $messages = [];

                foreach ($error as $errorItem) {
                    if ($errorItem instanceof FormError) {
                        $messages[] = $errorItem->getMessage();
                    }
                }

                $errorMessages[] = [
                    'name' => $error->getForm()->getName(),
                    'messages' => $messages,
                    'children' => $this->getErrorsRecursive($error),
                ];
            }
        }

        return $errorMessages;
    }
}

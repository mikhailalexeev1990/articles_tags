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
    public const EXTRA_CODE = 'InvalidFormDataException';

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
                $errorMessages[] = $this->formErrorIteratorMessages($error);
            } elseif ($error instanceof FormError) {
                $errorMessages[] = $this->formErrorMessages($error);
            }
        }

        foreach ($errorMessages as &$error) {
            $error['messages'] = array_unique($error['messages']);
        }

        return $errorMessages;
    }

    private function formErrorIteratorMessages(FormErrorIterator $error): array
    {
        $hasChildren = $error->hasChildren();
        $messages = [];

        foreach ($error as $errorItem) {
            if ($errorItem instanceof FormError) {
                $messages[] = $errorItem->getMessage();
            }
        }

        return [
            'name' => $error->getForm()->getName(),
            'messages' => $messages,
            'children' => $hasChildren ? $this->getErrorsRecursive($error) : [],
        ];
    }

    private function formErrorMessages(FormError $error): array
    {
        $origin = $error->getOrigin();
        $messages[] = $error->getMessage();

        $extraFields = $origin?->getErrors()->findByCodes([self::EXTRA_CODE])->getForm()->getExtraData();
        $keys = array_keys($extraFields);
        if ($extraFields !== null && !empty($keys)) {
            $messages[] = 'Addition fields: ' . implode(', ', $keys);
        }

        return [
            'name' => $origin?->getName(),
            'messages' => $messages,
            'children' => [],
        ];
    }
}

<?php

namespace App\Symfony\Event;

use App\Exception\InvalidDataExceptionInterface;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleErrorEvent;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

#[AsEventListener(event: KernelEvents::EXCEPTION)]
#[AsEventListener(event: ConsoleEvents::ERROR)]
final readonly class ExceptionEventSubscriber implements EventSubscriberInterface
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $data = [];
        $exception = $event->getThrowable();

        $debug = [
            'message' => $exception->getMessage(),
            'type' => get_class($exception),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => explode("\n", $exception->getTraceAsString()),
        ];

        $data['debug'] = $debug;

        if ($exception instanceof InvalidDataExceptionInterface) {
            $data['errors'] = $exception->getErrorMessages();
            $event->setResponse(new JsonResponse($data, $exception->getStatus()));

            return;
        }

        $data['errors'] = match (get_class($exception)) {
            NotFoundHttpException::class => ['Not found'],
            default => ['Unexpected error'],
        };

        $event->setResponse(new JsonResponse($data));
    }

    public function onConsoleError(ConsoleErrorEvent $event): void
    {
        $io = new SymfonyStyle($event->getInput(), $event->getOutput());
        $error = $event->getError();

        if ($error instanceof InvalidDataExceptionInterface) {
            foreach ($error->getErrorMessages() as $errorMessage) {
                foreach ($errorMessage['messages'] as $message) {
                    $io->error($errorMessage['name'].' - '.$message);
                }
            }
        }
    }

    /**
     * @return array<string, mixed>
     */
    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::EXCEPTION => 'onKernelException', ConsoleEvents::ERROR => 'onConsoleError'];
    }
}

<?php

namespace App\Infrastructure\Messenger\Middleware;

use App\Infrastructure\Messenger\Message\ProcessSheetImport;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;

final class MyMessengerMiddleware implements MiddlewareInterface
{
    public function __construct(private object $transport)
    {
    }

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        if ($envelope->getMessage() instanceof ProcessSheetImport) {
            return $this->transport->send($envelope);
        }

        return $stack->next()->handle($envelope, $stack);
    }
}

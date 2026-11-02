<?php

declare(strict_types=1);

error_reporting(E_ALL & ~E_DEPRECATED);

use App\Infrastructure\Messenger\Handler\ProcessSheetImportHandler;
use App\Infrastructure\Messenger\Message\ProcessSheetImport;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpTransportFactory;
use Symfony\Component\Messenger\Exception\MessageDecodingFailedException;
use Symfony\Component\Messenger\Handler\HandlersLocator;
use Symfony\Component\Messenger\MessageBus;
use Symfony\Component\Messenger\Middleware\HandleMessageMiddleware;
use Symfony\Component\Messenger\Transport\Serialization\PhpSerializer;

$container = require __DIR__ . '/app/container.php';

$dsn = getenv('MESSENGER_TRANSPORT_DSN');

if ($dsn === false || $dsn === '') {
    fwrite(STDERR, "MESSENGER_TRANSPORT_DSN is not set.\n");
    exit(1);
}

$transport = (new AmqpTransportFactory())->createTransport(
    $dsn,
    [],
    new PhpSerializer(),
);

$handlers = [
    ProcessSheetImport::class => [$container->get(ProcessSheetImportHandler::class)]
];

$bus = new MessageBus([
    new HandleMessageMiddleware(new HandlersLocator($handlers)),
]);

fwrite(STDOUT, "Waiting for messages...\n");

while (true) {
    foreach ($transport->get() as $envelope) {
        fwrite(STDOUT, 'Received: ' . get_debug_type($envelope->getMessage()) . "\n");

        $message = $envelope->getMessage();

        if ($message instanceof MessageDecodingFailedException) {
            fwrite(STDERR, "Could not decode queued message:\n");
            fwrite(STDERR, (string) $message . "\n");

            // Reject it so it doesn't keep reappearing.
            $transport->reject($envelope);
            continue;
        }

        try {
            $bus->dispatch($envelope);
            fwrite(STDOUT, "Handler completed\n");
            $transport->ack($envelope);
        } catch (Throwable $e) {
            fwrite(STDERR, "Handler failed:\n" . $e . "\n");
            $transport->reject($envelope);
        }
    }

    usleep(200_000);
}
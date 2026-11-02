<?php

declare(strict_types=1);

namespace App\Infrastructure\Messenger\Factory;

use App\Infrastructure\Messenger\Middleware\MyMessengerMiddleware;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpTransportFactory;
use Symfony\Component\Messenger\MessageBus;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Transport\Serialization\PhpSerializer;

final class MessengerBusFactory
{
    public function create(): MessageBusInterface
    {
        $dsn = getenv('MESSENGER_TRANSPORT_DSN')
            ?? throw new \RuntimeException('MESSENGER_TRANSPORT_DSN is not set.');

        $transport = (new AmqpTransportFactory())->createTransport(
            $dsn,
            [],
            new PhpSerializer(),
        );

        $bus = new MessageBus([new MyMessengerMiddleware($transport)]);

        return $bus;
    }
}

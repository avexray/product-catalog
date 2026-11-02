<?php

use App\Application\Service\FileStorageInterface;
use App\Application\Service\SheetImportService;
use App\Infrastructure\Storage\LocalFileStorage;
use DI\ContainerBuilder;
use App\Infrastructure\Messenger\Factory\MessengerBusFactory;
use Symfony\Component\Messenger\MessageBusInterface;
use function DI\autowire;
use function DI\factory;
use function DI\get;


return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        MessageBusInterface::class => factory([
            MessengerBusFactory::class,
            'create',
        ]),
        MessengerBusFactory::class => autowire(),

        SheetImportService::class => autowire(),
        FileStorageInterface::class => get(LocalFileStorage::class),
    ]);
};
<?php

declare(strict_types=1);

use App\Application\Service\FileStorageInterface;
use App\Application\Settings\SettingsInterface;
use App\Infrastructure\Parser\ProductSheetParser;
use App\Infrastructure\Storage\LocalFileStorage;
use DI\ContainerBuilder;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        LoggerInterface::class => function (ContainerInterface $c) {
            $settings = $c->get(SettingsInterface::class);

            $loggerSettings = $settings->get('logger');
            $logger = new Logger($loggerSettings['name']);

            $processor = new UidProcessor();
            $logger->pushProcessor($processor);

            $handler = new StreamHandler($loggerSettings['path'], $loggerSettings['level']);
            $logger->pushHandler($handler);

            return $logger;
        },
        ClientInterface::class => function (ContainerInterface $c): ClientInterface {
            return new Client([
                'connect_timeout' => 5,
                'timeout' => 10,
            ]);
        },
        FileStorageInterface::class => DI\get(LocalFileStorage::class)
    ]);
};

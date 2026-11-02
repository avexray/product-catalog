<?php

declare(strict_types=1);

use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMSetup;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        EntityManagerInterface::class => function (ContainerInterface $c) {
            $entityPaths = [__DIR__ . '/../src/Domain'];
            $isDevMode = getenv('APP_MODE') !== 'production';

            $config = ORMSetup::createAttributeMetadataConfiguration(
                $entityPaths,
                $isDevMode
            );

            $config->enableNativeLazyObjects(true);

            $connection = DriverManager::getConnection([
                'driver' => 'pdo_mysql',
                'host' => getenv('DATABASE_HOST'),
                'port' => getenv('DATABASE_PORT'),
                'dbname' => getenv('DATABASE_NAME'),
                'user' => getenv('DATABASE_USER'),
                'password' => getenv('DATABASE_PASSWORD'),
                'charset' => 'utf8mb4'
            ], $config);

            return new EntityManager($connection, $config);
        },
    ]);
};

<?php

declare(strict_types=1);

use App\Domain\ImportJob\ImportJobRepository;
use App\Domain\Product\ProductRepository;
use App\Infrastructure\Persistence\ImportJob\DbImportJobRepository;
use App\Infrastructure\Persistence\Product\DbProductRepository;
use DI\ContainerBuilder;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        ProductRepository::class => \DI\autowire(DbProductRepository::class),
        ImportJobRepository::class => \DI\autowire(DbImportJobRepository::class),
    ]);
};

<?php

declare(strict_types=1);
error_reporting(E_ALL & ~E_DEPRECATED);


use App\Domain\Product\Product;
use App\Domain\Product\ProductAttribute;
use App\Domain\Product\ProductImage;
use DI\ContainerBuilder;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory;

require __DIR__ . '/app/container.php';

// doctrine.php returns a function that registers definitions.
$containerBuilder = new ContainerBuilder();
$registerDoctrine = require __DIR__ . '/app/doctrine.php';
$registerDoctrine($containerBuilder);

$container = $containerBuilder->build();

/** @var EntityManagerInterface $entityManager */
$entityManager = $container->get(EntityManagerInterface::class);

$count = isset($argv[1]) ? (int) $argv[1] : 100;

if ($count < 1) {
    fwrite(STDERR, "Usage: php seed.php [number-of-products]\n");
    exit(1);
}

$faker = Factory::create();

for ($i = 0; $i < $count; $i++) {
    $product = new Product(
        externalCode: strtoupper($faker->unique()->bothify('SKU-########')),
        name: $faker->words(3, true),
        price: number_format($faker->randomFloat(2, 5, 500), 2, '.', ''),
        discount: number_format($faker->randomFloat(2, 0, 50), 2, '.', ''),
        description: $faker->paragraph(),
    );

    $entityManager->persist($product);

    $attributes = [
        'color' => $faker->safeColorName(),
        'size' => $faker->randomElement(['S', 'M', 'L', 'XL']),
    ];

    foreach ($attributes as $key => $value) {
        $entityManager->persist(
            new ProductAttribute(
                key: $key,
                value: $value,
                product: $product,
            ),
        );
    }

    $imageCount = $faker->numberBetween(1, 3);

    for ($imageIndex = 0; $imageIndex < $imageCount; $imageIndex++) {
        $imageId = $faker->uuid();

        $entityManager->persist(
            new ProductImage(
                url: $faker->imageUrl(800, 600),
                path: "products/{$imageId}.jpg",
                product: $product,
            ),
        );
    }
}

$entityManager->flush();

echo "Seeded {$count} products." . PHP_EOL;

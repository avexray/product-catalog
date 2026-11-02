<?php

declare(strict_types=1);

namespace App\Domain\Product;

interface ProductRepository
{
    public function findAll(
        int $page,
        int $perPage,
        ?string $name = null,
        ?float $minPrice = null,
        ?float $maxPrice = null
    ): array;

    public function countAll(
        ?string $name = null,
        ?float $minPrice = null,
        ?float $maxPrice = null
    ): int;

    public function findProductOfId(int $id): \App\Domain\Product\Product;
}

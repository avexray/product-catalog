<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Product;

use App\Domain\Product\Product;
use App\Domain\Product\ProductNotFoundException;
use App\Domain\Product\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;

class DbProductRepository implements ProductRepository
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function findAll(
        int $page,
        int $perPage,
        ?string $name = null,
        ?float $minPrice = null,
        ?float $maxPrice = null
    ): array {
        if ($page < 1 || $perPage < 1) {
            throw new \InvalidArgumentException('Page and perPage must be positive.');
        }

        $qb = $this->em->createQueryBuilder()
            ->select('p')
            ->from(Product::class, 'p');

        $this->applyFilters($qb, $name, $minPrice, $maxPrice);

        return $qb
            ->orderBy('p.id', 'ASC')
            ->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage)
            ->getQuery()
            ->getArrayResult();
    }

    public function countAll(
        ?string $name = null,
        ?float $minPrice = null,
        ?float $maxPrice = null
    ): int {
        $qb = $this->em->createQueryBuilder()
            ->select('COUNT(p.id)')
            ->from(Product::class, 'p');

        $this->applyFilters($qb, $name, $minPrice, $maxPrice);

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    private function applyFilters(
        \Doctrine\ORM\QueryBuilder $qb,
        ?string $name,
        ?float $minPrice,
        ?float $maxPrice
    ): void {
        if ($name !== null && trim($name) !== '') {
            $qb->andWhere('LOWER(p.name) LIKE LOWER(:name)')
                ->setParameter('name', '%' . trim($name) . '%');
        }

        if ($minPrice !== null && $maxPrice !== null) {
            $qb->andWhere('p.price BETWEEN :minPrice AND :maxPrice')
                ->setParameter('minPrice', $minPrice)
                ->setParameter('maxPrice', $maxPrice);
        } elseif ($minPrice !== null) {
            $qb->andWhere('p.price >= :minPrice')
                ->setParameter('minPrice', $minPrice);
        } elseif ($maxPrice !== null) {
            $qb->andWhere('p.price <= :maxPrice')
                ->setParameter('maxPrice', $maxPrice);
        }
    }

    public function findProductOfId(int $id): Product
    {
        $product = $this->em->createQueryBuilder()
            ->select('p')
            ->from(Product::class, 'p')
            ->leftJoin('p.attributes', 'a')
            ->leftJoin('p.images', 'i')
            ->where('p.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();

        if ($product === null) {
            throw new ProductNotFoundException();
        }

        return $product;
    }
}

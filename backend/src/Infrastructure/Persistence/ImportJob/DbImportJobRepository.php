<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\ImportJob;

use App\Domain\ImportJob\ImportJob;
use App\Domain\ImportJob\ImportJobRepository;
use Doctrine\ORM\EntityManagerInterface;

class DbImportJobRepository implements ImportJobRepository
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function findJobOfId(int $id): ?ImportJob
    {
        $importJob = $this->em->createQueryBuilder()
            ->select('j')
            ->from(ImportJob::class, 'j')
            ->where('j.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();

        return $importJob;
    }

    public function findLatest(): ?ImportJob
    {
        $importJob = $this->em->createQueryBuilder()
            ->select('j')
            ->from(ImportJob::class, 'j')
            ->orderBy('j.createdAt', 'DESC')
            ->addOrderBy('j.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return $importJob;
    }
}

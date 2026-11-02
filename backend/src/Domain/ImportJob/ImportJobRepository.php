<?php

declare(strict_types=1);

namespace App\Domain\ImportJob;

interface ImportJobRepository
{
    public function findJobOfId(int $id): ?ImportJob;
    public function findLatest(): ?ImportJob;
}

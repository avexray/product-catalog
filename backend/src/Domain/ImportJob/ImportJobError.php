<?php

namespace App\Domain\ImportJob;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;

#[Entity, Table(name: 'import_job_errors')]
final class ImportJobError
{
    #[Id, Column(type: 'integer'), GeneratedValue(strategy: 'AUTO')]
    private int $id;

    #[Column(name: 'source_row', type: 'integer')]
    private int $row;

    #[Column(name: 'error_text', type: 'text')]
    private string $error;

    #[ManyToOne(targetEntity: ImportJob::class)]
    #[JoinColumn(name: 'import_job_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ImportJob $importJob;

    public function __construct(int $row, string $error, ImportJob $importJob)
    {
        $this->row = $row;
        $this->error = $error;
        $this->importJob = $importJob;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getRow(): int
    {
        return $this->row;
    }

    public function getError(): string
    {
        return $this->error;
    }

    public function getImportJob(): ImportJob
    {
        return $this->importJob;
    }
}


<?php

namespace App\Domain\ImportJob;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Index;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\Table;
use Doctrine\Common\Collections\Collection;

#[Entity, Table(
    name: 'import_jobs',
    indexes: [
        new Index(name: 'idx_import_jobs_status_created', columns: ['status', 'created_at']),
    ]
)]
final class ImportJob
{
    public const STATUS_QUEUED = 'queued';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_COMPLETED_WITH_ERRORS = 'completed_with_errors';
    public const STATUS_FAILED = 'failed';

    #[Id, Column(type: 'integer'), GeneratedValue(strategy: 'AUTO')]
    private int $id;

    #[Column(name: 'original_filename', type: 'string', length: 255)]
    private string $originalFilename;

    #[Column(name: 'file_path', type: 'string', length: 1024)]
    private string $filePath;

    #[Column(type: 'string', length: 32)]
    private string $status = self::STATUS_QUEUED;

    #[Column(name: 'total_rows', type: 'integer')]
    private int $totalRows = 0;

    #[Column(name: 'processed_rows', type: 'integer')]
    private int $processedRows = 0;

    #[Column(name: 'successful_rows', type: 'integer')]
    private int $successfulRows = 0;

    #[Column(name: 'failed_rows', type: 'integer')]
    private int $failedRows = 0;

    #[Column(name: 'created_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[Column(name: 'updated_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $updatedAt;

    #[Column(name: 'finished_at', type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $finishedAt = null;

    #[OneToMany(
        mappedBy: 'importJob',
        targetEntity: ImportJobError::class,
        cascade: ['persist'],
        orphanRemoval: true
    )]
    private Collection $errors;

    public function getErrors(): Collection
    {
        return $this->errors;
    }

    public function addError(ImportJobError $error): void
    {
        if (!$this->errors->contains($error)) {
            $this->errors->add($error);
        }
    }

    public function __construct(string $originalFilename, string $filePath)
    {
        $now = new \DateTimeImmutable();

        $this->originalFilename = $originalFilename;
        $this->filePath = $filePath;
        $this->createdAt = $now;
        $this->updatedAt = $now;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOriginalFilename(): string
    {
        return $this->originalFilename;
    }

    public function getFilePath(): string
    {
        return $this->filePath;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getTotalRows(): int
    {
        return $this->totalRows;
    }

    public function getProcessedRows(): int
    {
        return $this->processedRows;
    }

    public function getSuccessfulRows(): int
    {
        return $this->successfulRows;
    }

    public function getFailedRows(): int
    {
        return $this->failedRows;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function getFinishedAt(): ?\DateTimeImmutable
    {
        return $this->finishedAt;
    }

    public function setStatus(string $status): self
    {
        $allowedStatuses = [
            self::STATUS_QUEUED,
            self::STATUS_PROCESSING,
            self::STATUS_COMPLETED,
            self::STATUS_COMPLETED_WITH_ERRORS,
            self::STATUS_FAILED,
        ];

        if (!in_array($status, $allowedStatuses, true)) {
            throw new \InvalidArgumentException('Invalid import status.');
        }

        $this->status = $status;
        $this->updatedAt = new \DateTimeImmutable();

        if (in_array($status, [
            self::STATUS_COMPLETED,
            self::STATUS_COMPLETED_WITH_ERRORS,
            self::STATUS_FAILED,
        ], true)) {
            $this->finishedAt = $this->updatedAt;
        }

        return $this;
    }

    public function setRowCounts(
        int $totalRows,
        int $processedRows,
        int $successfulRows,
        int $failedRows
    ): self {
        $this->totalRows = $totalRows;
        $this->processedRows = $processedRows;
        $this->successfulRows = $successfulRows;
        $this->failedRows = $failedRows;
        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }
}
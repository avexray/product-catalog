<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Domain\ImportJob\ImportJob;
use App\Infrastructure\Messenger\Message\ProcessSheetImport;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Http\Message\UploadedFileInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class SheetImportService
{
    private const MAX_FILE_SIZE_BYTES = 20 * 1024 * 1024; // 20 MB

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly FileStorageInterface   $fileStorage,
        private readonly MessageBusInterface    $messageBus,
    ) {
    }

    public function start(
        UploadedFileInterface $uploadedFile,
        string $originalFilename,
    ): ImportJob {
        $size = $uploadedFile->getSize();

        if ($size === null || $size <= 0) {
            throw new \InvalidArgumentException('The uploaded file is empty or its size is unknown.');
        }

        if ($size > self::MAX_FILE_SIZE_BYTES) {
            throw new \InvalidArgumentException('The file exceeds the 20 MB upload limit.');
        }

        $storageKey = $this->fileStorage->store($uploadedFile);

        $job = new ImportJob($originalFilename, $storageKey);

        try {
            $this->entityManager->persist($job);
            $this->entityManager->flush();

            $this->messageBus->dispatch(
                new ProcessSheetImport($job->getId())
            );
        } catch (\Throwable $e) {
            $this->fileStorage->delete($storageKey);
            throw $e;
        }

        return $job;
    }
}
<?php

declare(strict_types=1);

namespace App\Application\Actions\Import;

use App\Application\Actions\Action;
use App\Domain\ImportJob\ImportJobError;
use App\Domain\ImportJob\ImportJobRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;

class ViewImportAction extends Action
{
    private ImportJobRepository $importJobRepository;

    public function __construct(LoggerInterface $logger, ImportJobRepository $importJobRepository)
    {
        parent::__construct($logger);

        $this->importJobRepository = $importJobRepository;
    }

    protected function action(): Response
    {
        $job = $this->importJobRepository->findLatest();

        if ($job === null) {
            return $this->respondWithData();
        }

        $this->logger->info("Latest import operation `{$job->getId()}` was viewed.");

        return $this->respondWithData([
            'id' => $job->getId(),
            'originalFilename' => $job->getOriginalFilename(),
            'status' => $job->getStatus(),
            'totalRows' => $job->getTotalRows(),
            'processedRows' => $job->getProcessedRows(),
            'successfulRows' => $job->getSuccessfulRows(),
            'failedRows' => $job->getFailedRows(),
            'createdAt' => $job->getCreatedAt()->format(\DateTimeInterface::ATOM),
            'updatedAt' => $job->getUpdatedAt()->format(\DateTimeInterface::ATOM),
            'finishedAt' => $job->getFinishedAt()?->format(\DateTimeInterface::ATOM),

            'errors' => array_map(
                static fn (ImportJobError $error) => [
                    'row' => $error->getRow(),
                    'error' => $error->getError(),
                ],
                $job->getErrors()->toArray(),
            ),
        ]);
    }
}

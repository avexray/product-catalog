<?php

declare(strict_types=1);

namespace App\Application\Actions\Import;

use App\Application\Actions\Action;
use App\Application\Service\SheetImportService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Log\LoggerInterface;

class ImportSheetAction extends Action
{
    private readonly SheetImportService $importStarter;

    public function __construct(LoggerInterface $logger, SheetImportService $importStarter)
    {
        parent::__construct($logger);
        $this->importStarter = $importStarter;
    }

    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $uploadedFiles = $this->request->getUploadedFiles();
        $file = $uploadedFiles['file'] ?? null;

        if (!$file instanceof UploadedFileInterface) {
            return $this->respondWithData(
                ['status' => 'Upload an .xlsx file using the "file" field.'],
                400
            );
        }

        if ($file->getError() !== UPLOAD_ERR_OK) {
            return $this->respondWithData(
                ['status' => 'The file could not be uploaded.'],
                400
            );
        }

        $filename = $file->getClientFilename() ?? '';

        if (strtolower(pathinfo($filename, PATHINFO_EXTENSION)) !== 'xlsx') {
            return $this->respondWithData(
                ['status' => 'Only .xlsx files are accepted.'],
                400
            );
        }

        try {
            $job = $this->importStarter->start($file, $filename);
        } catch (\InvalidArgumentException $e) {
            return $this->respondWithData(
                ['status' => $e->getMessage()],
                400);
        } catch (\Throwable $e) {
            $this->logger->error('Could not start product import.', [
                'exception' => $e,
            ]);

            return $this->respondWithData(
                ['status' => 'The import could not be started.'],
                500
            );
        }

        $this->logger->info('Product import was queued.', [
            'import_job_id' => $job->getId(),
        ]);

        return $this->respondWithData([
            'id' => $job->getId(),
            'status' => $job->getStatus(),
        ], 202);
    }
}

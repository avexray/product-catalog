<?php

namespace App\Infrastructure\Messenger\Handler;

use App\Application\Service\FileStorageInterface;
use App\Domain\ImportJob\ImportJob;
use App\Domain\ImportJob\ImportJobError;
use App\Domain\ImportJob\ImportJobRepository;
use App\Domain\Product\Product;
use App\Domain\Product\ProductAttribute;
use App\Domain\Product\ProductImage;
use App\Infrastructure\Messenger\Message\ProcessSheetImport;
use App\Infrastructure\Parser\ProductSheetParser;
use App\Infrastructure\Parser\SheetRow;
use App\Infrastructure\Storage\ImageDownloadException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Exception\RecoverableMessageHandlingException;

#[AsMessageHandler]
final class ProcessSheetImportHandler
{
    public function __construct(
        private ImportJobRepository    $jobs,
        private FileStorageInterface   $storage,
        private EntityManagerInterface $entityManager
    )
    {
    }

    public function __invoke(ProcessSheetImport $message): void
    {
        $job = $this->jobs->findJobOfId($message->importJobId);
        if (!$job) {
            return;
        }

        $path = $this->storage->path($job->getFilePath());

        if (!is_file($path)) {
            $job->setStatus(ImportJob::STATUS_FAILED);
            $this->entityManager->flush();
            return;
        }

        try {
            $totalRows = 0;
            $processedRows = 0;
            $successfulRows = 0;
            $failedRows = 0;

            $parser = new ProductSheetParser();

            foreach ($parser->parse($path) as $parsed) {
                $totalRows++;
                $processedRows++;

                if (!$parsed->isValid()) {
                    $failedRows++;
                    $this->saveError($message->importJobId, $parsed);
                    $this->saveProgress($message->importJobId, $totalRows, $processedRows, $successfulRows, $failedRows);
                    continue;
                }

                try {
                    $imagePaths = $this->downloadImages($parsed->imageLinks);

                    $this->entityManager->getConnection()->beginTransaction();
                    try {
                        $this->saveProduct($parsed, $imagePaths);

                        $this->entityManager->flush();
                        $this->entityManager->getConnection()->commit();
                        $successfulRows++;
                    } catch (\Throwable $e) {
                        if ($this->entityManager->getConnection()->isTransactionActive()) {
                            $this->entityManager->getConnection()->rollBack();
                        }
                        throw $e;
                    }
                } catch (\Throwable $e) {
                    $failedRows++;

                    if (!$this->entityManager->isOpen()) {
                        throw new RecoverableMessageHandlingException(
                            'EntityManager closed due to DB error. Retrying...',
                            previous: $e,
                        );
                    }

                    $this->saveError(
                        $message->importJobId,
                        $parsed,
                        $e->getMessage(),
                    );
                }

                $this->saveProgress($message->importJobId, $totalRows, $processedRows, $successfulRows, $failedRows);
                $this->entityManager->clear();
            }

            $this->updateStatus($message->importJobId, ImportJob::STATUS_COMPLETED);
        } catch (RecoverableMessageHandlingException $e) {
            throw $e;
        } catch (\Throwable $e) {
            $this->updateStatus($message->importJobId, ImportJob::STATUS_FAILED);
            throw $e;
        }
    }

    private function saveProduct(SheetRow $parsed, array $imagePaths)
    {
        /** @var Product $product */
        $product = $this->entityManager
            ->getRepository(Product::class)
            ->findOneBy(['externalCode' => $parsed->externalCode]);

        if ($product === null) {
            $product = new Product(
                externalCode: $parsed->externalCode,
                name: $parsed->name,
                price: $parsed->sellingPrice,
                discount: $this->calculateDiscount(
                    $parsed->sellingPrice,
                    $parsed->costPrice,
                ),
                description: $parsed->description,
            );

            $this->entityManager->persist($product);
        } else {
            $product->setName($parsed->name);
            $product->setPrice($parsed->sellingPrice);
            $product->setDiscount(
                $this->calculateDiscount($parsed->sellingPrice, $parsed->costPrice),
            );
            $product->setDescription($parsed->description);


            foreach ($product->getAttributes()->toArray() as $attribute) {
                $product->removeAttribute($attribute);
                $this->entityManager->remove($attribute);
            }

            foreach ($product->getImages()->toArray() as $image) {
                $product->removeImage($image);
                $this->entityManager->remove($image);
            }
        }

        foreach ($parsed->attributes as $key => $value) {
            $this->entityManager->persist(
                new ProductAttribute(key: $key, value: $value, product: $product),
            );
        }

        foreach ($imagePaths as $imageData) {
            $this->entityManager->persist(
                new ProductImage(
                    url: $imageData['url'],
                    path: $imageData['path'],
                    product: $product,
                ),
            );
        }
    }

    private function calculateDiscount(string|float $sellingPrice, string|float $costPrice): string
    {
        $sellingPrice = (float) $sellingPrice;
        $costPrice = (float) $costPrice;

        if ($sellingPrice == 0.0) {
            return '0.00';
        }

        return number_format(
            (($sellingPrice - $costPrice) / $sellingPrice) * 100,
            2,
            '.',
            ''
        );
    }

    private function saveProgress(int $jobId, int $totalRows, int $processedRows,
                                  int $successfulRows, int $failedRows): void
    {
        $job = $this->jobs->findJobOfId($jobId);
        $job->setRowCounts(
            totalRows: $totalRows,
            processedRows: $processedRows,
            successfulRows: $successfulRows,
            failedRows: $failedRows
        );
        $this->entityManager->flush();
    }

    private function saveError(int $jobId, SheetRow $row, ?string $customError = null): void
    {
        $job = $this->jobs->findJobOfId($jobId);

        if ($customError !== null) {
            $job->addError(new ImportJobError($row->rowNumber, $customError, $job));
        } else {
            foreach ($row->errors() as $errorText) {
                $job->addError(new ImportJobError($row->rowNumber, $errorText, $job));
            }
        }

        $this->entityManager->flush();
    }

    private function updateStatus(int $jobId, string $status): void
    {
        if (!$this->entityManager->isOpen()) {
            return;
        }
        $job = $this->jobs->findJobOfId($jobId);
        $job->setStatus($status);
        $this->entityManager->flush();
    }

    private function downloadImages(array $imageLinks): array
    {
        $imagePaths = [];

        foreach ($imageLinks as $url) {
            try {
                $path = $this->storage->storeFromUrl($url);
            } catch (ImageDownloadException $e) {
                throw new ImageDownloadException(
                    sprintf('Изображение "%s": %s', $url, $e->getMessage()),
                    previous: $e,
                );
            }

            $imagePaths[] = [
                'url' => $url,
                'path' => $path,
            ];
        }

        return $imagePaths;
    }
}

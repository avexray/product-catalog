<?php

declare(strict_types=1);

namespace App\Application\Service;

use Psr\Http\Message\UploadedFileInterface;


interface FileStorageInterface
{
    public function store(UploadedFileInterface $file): string;

    public function storeFromUrl(string $url): string;

    public function path(string $storageKey): string;

    public function delete(string $storageKey): void;
}
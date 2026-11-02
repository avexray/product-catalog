<?php

declare(strict_types=1);

namespace App\Infrastructure\Storage;

use App\Application\Service\FileStorageInterface;
use GuzzleHttp\ClientInterface;
use Psr\Http\Message\UploadedFileInterface;
use RuntimeException;

final class LocalFileStorage implements FileStorageInterface
{
    private string $directory = '/application/var/storage';

    public function __construct(private ClientInterface $httpClient)
    {
    }

    public function store(UploadedFileInterface $file): string
    {
        if (!is_dir($this->directory)
            && !mkdir($this->directory, 0700, true)
            && !is_dir($this->directory)
        ) {
            throw new RuntimeException('Не удалось создать директорию для загрузки файла.');
        }

        $key = bin2hex(random_bytes(16)) . '.xlsx';
        $target = rtrim($this->directory, DIRECTORY_SEPARATOR)
            . DIRECTORY_SEPARATOR
            . $key;

        try {
            $file->moveTo($target);
        } catch (\Throwable $e) {
            throw new RuntimeException('Не удалось загрузить файл.', 0, $e);
        }

        return $key;
    }

    public function storeFromUrl(string $url): string
    {
        $parts = parse_url($url);

        if ($parts === false) {
            throw new ImageDownloadException('Неверный формат URL.');
        }

        $scheme = strtolower($parts['scheme'] ?? '');

        if (!in_array($scheme, ['http', 'https'], true) || empty($parts['host'])) {
            throw new ImageDownloadException('URL изображения должен использовать HTTP или HTTPS.');
        }

        $response = $this->httpClient->request('GET', $url, [
            'allow_redirects' => false,
            'connect_timeout' => 5,
            'timeout' => 10,
            'http_errors' => false,
        ]);

        if ($response->getStatusCode() !== 200) {
            throw new ImageDownloadException('Не удалось загрузить изображение.');
        }

        $contentLength = $response->getHeaderLine('Content-Length');

        if ($contentLength !== '' && (int) $contentLength > 10_000_000) {
            throw new ImageDownloadException('Изображение слишком большое.');
        }

        $contents = $response->getBody()->getContents();

        if ($contents === '' || strlen($contents) > 10_000_000) {
            throw new ImageDownloadException('Изображение слишком большое или имеет неверный формат.');
        }

        // Detect the actual image format from its bytes, not the response Content-Type.
        $imageInfo = @getimagesizefromstring($contents);

        if ($imageInfo === false) {
            throw new ImageDownloadException('Файл не является изображением.');
        }

        $extensions = [
            IMAGETYPE_JPEG => 'jpg',
            IMAGETYPE_PNG  => 'png',
            IMAGETYPE_GIF  => 'gif',
            IMAGETYPE_WEBP => 'webp',
        ];

        $imageType = $imageInfo[2];

        if (!isset($extensions[$imageType])) {
            throw new ImageDownloadException('Формат изображения не поддерживается.');
        }

        if (!is_dir($this->directory)
            && !mkdir($this->directory, 0775, true)
            && !is_dir($this->directory)
        ) {
            throw new ImageDownloadException('Не удалось создать директорию для загрузки изображения.');
        }

        $key = bin2hex(random_bytes(16)) . '.' . $extensions[$imageType];
        $destination = rtrim($this->directory, DIRECTORY_SEPARATOR)
            . DIRECTORY_SEPARATOR
            . $key;

        if (file_put_contents($destination, $contents) === false) {
            throw new ImageDownloadException('Не удалось сохранить загруженное изображение.');
        }

        return $key;
    }


    public function path(string $storageKey): string
    {
        $path = rtrim($this->directory, DIRECTORY_SEPARATOR)
            . DIRECTORY_SEPARATOR
            . $storageKey;

        return $path;
    }

    public function delete(string $storageKey): void
    {
        if (!preg_match('/\A[a-f0-9]{32}\.xlsx\z/', $storageKey)) {
            throw new RuntimeException('Неверное имя файла.');
        }

        $path = rtrim($this->directory, DIRECTORY_SEPARATOR)
            . DIRECTORY_SEPARATOR
            . $storageKey;

        if (is_file($path) && !unlink($path)) {
            throw new RuntimeException('Не удалось удалить файл.');
        }
    }
}
<?php

namespace App\Infrastructure\Messenger\Message;

final readonly class ProcessSheetImport
{
    public function __construct(public int $importJobId)
    {
    }
}


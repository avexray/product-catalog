<?php

namespace App\Infrastructure\Parser;

final readonly class SheetRow
{
    public function __construct(
        public ?string $externalCode,
        public ?string $name,
        public ?string $description,
        public ?string $sellingPrice,
        public ?string $costPrice,
        public array $attributes,
        public array $imageLinks,
        public int $rowNumber
    ) {
    }

    public function isValid(): bool
    {
        return $this->externalCode !== null
            && trim($this->externalCode) !== ''
            && $this->name !== null
            && trim($this->name) !== ''
            && $this->sellingPrice !== null
            && $this->costPrice !== null;
    }

    public function errors(): array
    {
        $errors = [];
        if ($this->externalCode === null || trim($this->externalCode) === '') {
            $errors[] = 'Некорректное поле Внешний код';
        }

        if ($this->name === null || trim($this->name) === '') {
            $errors[] = 'Некорректное поле Название';
        }

        if ($this->sellingPrice === null || trim($this->sellingPrice) === '') {
            $errors[] = 'Некорректное поле Цена продажи';
        }

        if ($this->costPrice === null || trim($this->costPrice) === '') {
            $errors[] = 'Некорректное поле Себестоимость';
        }

        return $errors;
    }
}
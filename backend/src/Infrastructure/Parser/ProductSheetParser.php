<?php

namespace App\Infrastructure\Parser;

use PhpOffice\PhpSpreadsheet\IOFactory;

final class ProductSheetParser
{
    private ?SheetHeader $header = null;
    private array $attributeColumns = [];
    private ?int $imageLinksColumn = null;

    public function parse(string $path): \Generator
    {
        $reader = IOFactory::createReaderForFile($path);
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($path);

        try {
            $sheet = $spreadsheet->getActiveSheet();

            $rows = $sheet->rangeToArrayYieldRows(
                'A1:' . $sheet->getHighestDataColumn() . $sheet->getHighestDataRow(),
                null,
                false,
                false,
            );

            $rowNumber = 0;

            foreach ($rows as $row) {
                ++$rowNumber;
                $row = array_values($row);

                if ($this->header === null) {
                    $this->parseHeader($row);

                    if (!$this->header->isValid()) {
                        throw new \UnexpectedValueException('Header is missing required columns.');
                    }
                    continue;
                }

                if ($this->isBlankRow($row)) {
                    continue;
                }

                $externalCode = trim((string) $this->value($row, ProductField::EXTERNAL_CODE));
                $name = trim((string) $this->value($row, ProductField::NAME));
                $description = trim((string) $this->value($row, ProductField::DESCRIPTION));
                $sellingPrice = $this->decimal($this->value($row, ProductField::SELLING_PRICE));
                $costPrice = $this->decimal($this->value($row, ProductField::COST_PRICE));

                $attributes = [];

                foreach ($this->attributeColumns as $key => $index) {
                    $value = trim((string) ($row[$index] ?? ''));

                    if ($value !== '') {
                        $attributes[$key] = $value;
                    }
                }

                $imageLinks = [];

                if ($this->imageLinksColumn !== null) {
                    $cell = trim((string) ($row[$this->imageLinksColumn] ?? ''));

                    if ($cell !== '') {
                        $imageLinks = array_map('trim', explode(',', $cell));
                    }
                }

                yield new SheetRow(
                    externalCode: $externalCode,
                    name: $name,
                    description: $description,
                    sellingPrice: $sellingPrice,
                    costPrice: $costPrice,
                    attributes: $attributes,
                    imageLinks: $imageLinks,
                    rowNumber: $rowNumber
                );
            }

            if ($this->header === null) {
                throw new \UnexpectedValueException('The import file is empty.');
            }
        } finally {
            $spreadsheet->disconnectWorksheets();
        }
    }

    private function parseHeader(array $row): void
    {
        $this->header = new SheetHeader();
        $this->attributeColumns = [];
        $this->imageLinksColumn = null;

        foreach ($row as $index => $value) {
            $header = trim((string) $value);

            if ($header === '') {
                continue;
            }

            if ($header === 'Доп. поле: Ссылки на фото') {
                $this->imageLinksColumn = $index;
                continue;
            }

            if (str_starts_with($header, 'Доп. поле:')) {
                $name = trim(substr($header, strlen('Доп. поле:')));

                if ($name !== '') {
                    $this->attributeColumns[$name] = $index;
                }

                continue;
            }

            $this->header->add($index, $header);
        }
    }

    private function value(array $row, ProductField $field): mixed
    {
        return $row[$this->header->productColumn($field)] ?? null;
    }

    private function isBlankRow(array $row): bool
    {
        foreach ($row as $value) {
            if ($value !== null && trim((string) $value) !== '') {
                return false;
            }
        }

        return true;
    }

    private function decimal(mixed $value): ?string
    {
        if ($value === null || trim((string) $value) === '') {
            return null;
        }

        $value = str_replace(',', '.', trim((string) $value));

        if (!is_numeric($value)) {
            return null;
        }

        return number_format((float) $value, 2, '.', '');
    }
}

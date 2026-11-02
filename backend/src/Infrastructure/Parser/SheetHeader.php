<?php

namespace App\Infrastructure\Parser;

final class SheetHeader
{
    private array $productColumns = [];
    private array $additionalAttributes = [];

    public function add(int $index, string $value): void
    {
        $value = trim($value);

        $productField = ProductField::tryFrom($value);
        if ($productField !== null) {
            $this->productColumns[$productField->value] = $index;
            return;
        }

        if (preg_match('/^Доп\.?\s*поле\s*:\s*(.+)$/u', $value, $matches) === 1) {
            $name = trim($matches[1]);

            if ($name !== '') {
                $this->additionalAttributes[] = [
                    'name' => $name,
                    'index' => $index,
                ];
            }
        }
    }

    public function productColumn(ProductField $field): ?int
    {
        return $this->productColumns[$field->value] ?? null;
    }

    /** @return array<string, int> */
    public function productColumns(): array
    {
        return $this->productColumns;
    }

    /** @return list<array{name: string, index: int}> */
    public function additionalAttributes(): array
    {
        return $this->additionalAttributes;
    }

    public function isValid(): bool
    {
        foreach (ProductField::cases() as $field) {
            if (!array_key_exists($field->value, $this->productColumns)) {
                return false;
            }
        }

        return true;
    }
}

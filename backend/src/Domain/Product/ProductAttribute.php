<?php

declare(strict_types=1);

namespace App\Domain\Product;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;

#[Entity, Table(name: 'product_attributes')]
final class ProductAttribute
{
    #[Id, Column(type: 'integer'), GeneratedValue(strategy: 'AUTO')]
    private int $id;

    #[Column(name: 'attribute_key', type: 'string', nullable: false)]
    private string $key;

    #[Column(name: 'attribute_value', type: 'string', nullable: false)]
    private string $value;

    #[ManyToOne(targetEntity: Product::class, inversedBy: 'attributes')]
    #[JoinColumn(
        name: 'product_id',
        referencedColumnName: 'id',
        nullable: false,
        onDelete: 'CASCADE',
    )]
    private Product $product;

    public function __construct(string $key, string $value, Product $product)
    {
        $this->key = $key;
        $this->value = $value;
        $this->product = $product;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }
}

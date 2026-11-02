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

#[Entity, Table(name: 'product_images')]
final class ProductImage
{
    #[Id, Column(type: 'integer'), GeneratedValue(strategy: 'AUTO')]
    private int $id;

    #[Column(type: 'text', nullable: false)]
    private string $url;

    #[Column(type: 'string', nullable: false)]
    private string $path;

    #[ManyToOne(targetEntity: Product::class, inversedBy: 'images')]
    #[JoinColumn(
        name: 'product_id',
        referencedColumnName: 'id',
        nullable: false,
        onDelete: 'CASCADE',
    )]
    private Product $product;

    public function __construct(string $url, string $path, Product $product)
    {
        $this->url = $url;
        $this->path = $path;
        $this->product = $product;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getPath(): string
    {
        return 'http://localhost/storage/' . $this->path;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }
}

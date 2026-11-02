<?php

declare(strict_types=1);

namespace App\Domain\Product;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\Table;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[Entity, Table(name: 'products')]
final class Product
{
    #[Id, Column(type: 'integer'), GeneratedValue(strategy: 'AUTO')]
    private int $id;

    #[Column(name: 'external_code', type: 'string', unique: true, nullable: false)]
    private string $externalCode;

    #[Column(type: 'string', nullable: false)]
    private string $name;

    #[Column(type: 'text', nullable: true)]
    private ?string $description;

    #[Column(type: 'decimal', precision: 10, scale: 2, nullable: false)]
    private string $price;

    #[Column(type: 'decimal', precision: 5, scale: 2, nullable: false)]
    private string $discount;

    #[OneToMany(
        targetEntity: ProductAttribute::class,
        mappedBy: 'product',
        cascade: ['persist'],
        orphanRemoval: true,
    )]
    private Collection $attributes;

    #[OneToMany(
        targetEntity: ProductImage::class,
        mappedBy: 'product',
        cascade: ['persist'],
        orphanRemoval: true,
    )]
    private Collection $images;

    public function __construct(
        string $externalCode,
        string $name,
        string $price,
        string $discount,
        ?string $description = null,
    ) {
        $this->externalCode = $externalCode;
        $this->name = $name;
        $this->price = $price;
        $this->discount = $discount;
        $this->description = $description;
        $this->attributes = new ArrayCollection();
        $this->images = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getExternalCode(): string
    {
        return $this->externalCode;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getPrice(): string
    {
        return $this->price;
    }

    public function setPrice(string $price): void
    {
        $this->price = $price;
    }

    public function getDiscount(): string
    {
        return $this->discount;
    }

    public function setDiscount(string $discount): void
    {
        $this->discount = $discount;
    }

    public function getAttributes(): Collection
    {
        return $this->attributes;
    }

    public function getImages(): Collection
    {
        return $this->images;
    }

    public function removeAttribute(ProductAttribute $attribute): void
    {
        $this->attributes->removeElement($attribute);
    }

    public function removeImage(ProductImage $image): void
    {
        $this->images->removeElement($image);
    }
}

<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    public const TYPE_PHYSICAL = 'physical';
    public const TYPE_DIGITAL = 'digital';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le nom du produit est obligatoire', groups: ['details'])]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\NotBlank(message: 'Le prix est obligatoire', groups: ['details'])]
    #[Assert\Positive(message: 'Le prix doit être positif', groups: ['details'])]
    private ?string $price = null;

    #[ORM\Column(length: 20)]
    private ?string $productType = self::TYPE_PHYSICAL;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $weight = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $dimensions = null;

    #[ORM\Column(nullable: true)]
    private ?int $stock = null;

    #[ORM\Column(nullable: true)]
    private ?int $licenseDuration = null;

    #[ORM\Column(nullable: true)]
    private ?int $maxActivations = null;

    #[ORM\Column]
    private bool $highValueConfirmed = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): static
    {
        $this->price = $price;
        return $this;
    }

    public function getProductType(): ?string
    {
        return $this->productType;
    }

    public function setProductType(string $productType): static
    {
        $this->productType = $productType;
        return $this;
    }

    public function getWeight(): ?string
    {
        return $this->weight;
    }

    public function setWeight(?string $weight): static
    {
        $this->weight = $weight;
        return $this;
    }

    public function getDimensions(): ?string
    {
        return $this->dimensions;
    }

    public function setDimensions(?string $dimensions): static
    {
        $this->dimensions = $dimensions;
        return $this;
    }

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock(?int $stock): static
    {
        $this->stock = $stock;
        return $this;
    }

    public function getLicenseDuration(): ?int
    {
        return $this->licenseDuration;
    }

    public function setLicenseDuration(?int $licenseDuration): static
    {
        $this->licenseDuration = $licenseDuration;
        return $this;
    }

    public function getMaxActivations(): ?int
    {
        return $this->maxActivations;
    }

    public function setMaxActivations(?int $maxActivations): static
    {
        $this->maxActivations = $maxActivations;
        return $this;
    }

    public function isHighValueConfirmed(): bool
    {
        return $this->highValueConfirmed;
    }

    public function setHighValueConfirmed(bool $highValueConfirmed): static
    {
        $this->highValueConfirmed = $highValueConfirmed;
        return $this;
    }

    public function isPhysical(): bool
    {
        return $this->productType === self::TYPE_PHYSICAL;
    }

    public function isDigital(): bool
    {
        return $this->productType === self::TYPE_DIGITAL;
    }

    public function isHighValue(): bool
    {
        return (float) $this->price > 1000;
    }
}

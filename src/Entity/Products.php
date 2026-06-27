<?php

namespace src\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(
        name: "products",
        indexes: [
            new ORM\Index(
                name: "ft_product_name_market_brand",
                columns: ["product_name", "market", "brand"],
                flags: ["fulltext"]
            )
        ]
    )
]
class Products {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 500)]
    private string $productName;

    #[ORM\Column(type: "string", length: 500)]
    private string $market;

    #[ORM\Column(type: "string", length: 500)]
    private string $brand;

    #[ORM\Column(type: "integer")]
    private int $kcal;

    #[ORM\Column(type: "decimal", precision: 10, scale: 2)]
    private string $protein;

    #[ORM\Column(type: "decimal", precision: 10, scale: 2)]
    private string $carbs;

    #[ORM\Column(type: "decimal", precision: 10, scale: 2)]
    private string $fats;

    #[ORM\Column(type: "decimal", precision: 10, scale: 2)]
    private string $fiber;

    public function __construct() {
        $this->kcal = 0;
        $this->protein = '0.00';
        $this->carbs = '0.00';
        $this->fats = '0.00';
        $this->fiber = '0.00';
        $this->productName = '';
        $this->market = '';
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getProductName(): string {
        return $this->productName;
    }

    public function setProductName(string $productName): self {
        $this->productName = $productName;
        return $this;
    }

    public function getMarket(): string {
        return $this->market;
    }

    public function setMarket(string $market): self {
        $this->market = $market;
        return $this;
    }

    public function getBrand(): string {
        return $this->brand;
    }

    public function setBrand(string $brand): void {
        $this->brand = $brand;
    }

    public function getKcal(): int {
        return $this->kcal;
    }

    public function setKcal(int $kcal): self {
        $this->kcal = $kcal;
        return $this;
    }

    public function getProtein(): float {
        return (float) $this->protein;
    }

    public function setProtein(float|string $protein): self {
        $this->protein = number_format((float) $protein, 2, '.', '');
        return $this;
    }

    public function getCarbs(): float {
        return (float) $this->carbs;
    }

    public function setCarbs(float|string $carbs): self {
        $this->carbs = number_format((float) $carbs, 2, '.', '');
        return $this;
    }

    public function getFats(): float {
        return (float) $this->fats;
    }

    public function setFats(float|string $fats): self {
        $this->fats = number_format((float) $fats, 2, '.', '');
        return $this;
    }

    public function getFiber(): float {
        return (float) $this->fiber;
    }

    public function setFiber(float|string $fiber): self {
        $this->fiber = number_format((float) $fiber, 2, '.', '');
        return $this;
    }
}

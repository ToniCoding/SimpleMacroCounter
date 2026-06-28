<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class ProductsDTO {
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        private string $productName = '',

        #[Assert\Type('string')]
        private string $market = 'Generico',

        #[Assert\Type('string')]
        private string $brand = 'Usuario',

        #[Assert\NotNull]
        #[Assert\PositiveOrZero]
        private float $protein = 0,

        #[Assert\NotNull]
        #[Assert\PositiveOrZero]
        private float $carbs = 0,

        #[Assert\NotNull]
        #[Assert\PositiveOrZero]
        private float $fats = 0,

        #[Assert\NotNull]
        #[Assert\PositiveOrZero]
        private float $fiber = 0
    ) {}

    public function getProductName(): string {
        return $this->productName;
    }

    public function setProductName(string $productName): void {
        $this->productName = $productName;
    }

    public function getMarket(): string {
        return $this->market;
    }

    public function setMarket(string $market): void {
        $this->market = $market;
    }

    public function getBrand(): string {
        return $this->brand;
    }

    public function setBrand(string $brand): void {
        $this->brand = $brand;
    }

    public function getProtein(): float {
        return $this->protein;
    }
    public function setProtein(float $protein): void {
        $this->protein = $protein;
    }

    public function getCarbs(): float {
        return $this->carbs;
    }

    public function setCarbs(float $carbs): void {
        $this->carbs = $carbs;
    }

    public function getFats(): float {
        return $this->fats;
    }

    public function setFats(float $fats): void {
        $this->fats = $fats;
    }

    public function getFiber(): float {
        return $this->fiber;
    }

    public function setFiber(float $fiber): void {
        $this->fiber = $fiber;
    }

    public function toString(): string {
        return 'Product: ' . $this->getProductName() .
        "\n\tMarket: " . $this->getMarket() .
        "\n\tBrand: " . $this->getBrand() .
        "\n\tProtein: " . $this->getProtein() .
        "\n\tCarbs: " . $this->getCarbs() .
        "\n\tFats: " . $this->getFats() .
        "\n\tFiber: " . $this->getFiber();
    }
}
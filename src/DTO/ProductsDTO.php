<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class ProductsDTO {
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        public readonly string $productName = '',

        #[Assert\Type('string')]
        public readonly string $market = 'Generico',

        #[Assert\Type('string')]
        public readonly string $brand = 'Usuario',

        #[Assert\NotNull]
        #[Assert\PositiveOrZero]
        public readonly float $protein = 0,

        #[Assert\NotNull]
        #[Assert\PositiveOrZero]
        public readonly float $carbs = 0,

        #[Assert\NotNull]
        #[Assert\PositiveOrZero]
        public readonly float $fats = 0,

        #[Assert\NotNull]
        #[Assert\PositiveOrZero]
        public readonly float $fiber = 0
    ) {}

    public function toString(): string {
        return 'Product: ' . $this->productName .
        "\n\tMarket: " . $this->market .
        "\n\tBrand: " . $this->brand .
        "\n\tProtein: " . $this->protein .
        "\n\tCarbs: " . $this->carbs .
        "\n\tFats: " . $this->fats .
        "\n\tFiber: " . $this->fiber;
    }
}

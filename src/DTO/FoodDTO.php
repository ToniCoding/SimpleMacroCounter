<?php

namespace src\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class FoodDTO {
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        private string $name = '',

        #[Assert\NotBlank]
        #[Assert\Type('string')]
        private string $market = '',

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

    public function getName(): string {
        return $this->name;
    }
    public function setName(string $name): void {
        $this->name = $name;
    }

    public function getMarket(): string {
        return $this->market;
    }
    public function setMarket(string $market): void {
        $this->market = $market;
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
        return 'Name: ' . $this->getName() .
        "\n\tMarket: " . $this->getMarket() .
        "\n\tProtein: " . $this->getProtein() .
        "\n\tCarbs: " . $this->getCarbs() .
        "\n\tFats: " . $this->getFats() .
        "\n\tFiber: " . $this->getFiber();
    }
}

<?php

namespace src\DTO;

use Symfony\Component\Validator\Constraints as Assert;
class FoodDTO
{
    #[Assert\NotBlank]
    private string $name;

    #[Assert\NotBlank]
    private string $market;

    #[Assert\NotNull]
    #[Assert\PositiveOrZero]
    private float $protein;

    #[Assert\NotNull]
    #[Assert\PositiveOrZero]
    private float $carbs;

    #[Assert\NotNull]
    #[Assert\PositiveOrZero]
    private float $fats;

    #[Assert\NotNull]
    #[Assert\PositiveOrZero]
    private float $fiber;

    public function __construct(
        string $name = '',
        string $market = '',
        float $protein = 0,
        float $carbs = 0,
        float $fats = 0,
        float $fiber = 0
    ) {
        $this->name = $name;
        $this->market = $market;
        $this->protein = $protein;
        $this->carbs = $carbs;
        $this->fats = $fats;
        $this->fiber = $fiber;
    }

    public function getProtein(): float { return $this->protein; }
    public function setProtein(float $protein): void { $this->protein = $protein; }

    public function getCarbs(): float { return $this->carbs; }
    public function setCarbs(float $carbs): void { $this->carbs = $carbs; }

    public function getFats(): float { return $this->fats; }
    public function setFats(float $fats): void { $this->fats = $fats; }

    public function getFiber(): float { return $this->fiber; }
    public function setFiber(float $fiber): void { $this->fiber = $fiber; }

    public function getName(): string { return $this->name; }
    public function setName(string $name): void { $this->name = $name; }

    public function getMarket(): string { return $this->market; }
    public function setMarket(string $market): void { $this->market = $market; }
}


<?php

namespace src\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class FoodDTO {
    public function __construct(string $name = '', string $market = '', int $protein = 0, int $carbs = 0, int $fats = 0, int $fiber = 0) {
        $this->name = $name;
        $this->market = $market;
        $this->protein = $protein;
        $this->carbs = $carbs;
        $this->fats = $fats;
        $this->fiber = $fiber;
    }

    #[Assert\NotBlank]
    private string $name;

    #[Assert\NotBlank]
    private string $market;

    #[Assert\NotNull]
    #[Assert\PositiveOrZero]
    private int $protein;

    #[Assert\NotNull]
    #[Assert\PositiveOrZero]
    private int $carbs;

    #[Assert\NotNull]
    #[Assert\PositiveOrZero]
    private int $fats;

    #[Assert\NotNull]
    #[Assert\PositiveOrZero]
    private int $fiber;

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

    public function getProtein(): int {
        return $this->protein;
    }

    public function setProtein(int $protein): void {
        $this->protein = $protein;
    }

    public function getCarbs(): int {
        return $this->carbs;
    }

    public function setCarbs(int $carbs): void {
        $this->carbs = $carbs;
    }

    public function getFats(): int {
        return $this->fats;
    }

    public function setFats(int $fats): void {
        $this->fats = $fats;
    }
    
    public function getFiber(): int {
        return $this->fiber;
    }

    public function setFiber(int $fiber): void {
        $this->fiber = $fiber;
    }
}

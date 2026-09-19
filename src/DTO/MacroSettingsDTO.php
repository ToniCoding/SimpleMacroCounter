<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class MacroSettingsDTO {
    public function __construct(
        #[Assert\NotNull]
        #[Assert\PositiveOrZero]
        private float $calories = 0,

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

    public function getCalories(): int {
        return $this->calories;
    }
    public function setCalories(int $calories): void {
        $this->calories = $calories;
    }

    public function __toArray() {
        return [
            'calories' => $this->getCalories(),
            'protein' => $this->getProtein(),
            'carbs' => $this->getCarbs(),
            'fats' => $this->getFats(),
            'fiber' => $this->getFiber()
        ];
    }

    public function __toString() {
        return 'Calories: ' . $this->getCalories() .
        "\n\tProtein: " . $this->getProtein() .
        "\n\tCarbs: " . $this->getCarbs() .
        "\n\tFats: " . $this->getFats() .
        "\n\tFiber: " . $this->getFiber();
    }
}

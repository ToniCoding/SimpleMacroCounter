<?php

namespace src\DTO;

class MacroDataDTO {
    public function __construct(
        public int $protein = 0,
        public int $carbs = 0,
        public int $fats = 0,
        public int $fiber = 0,
        public int $calories = 0
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
}

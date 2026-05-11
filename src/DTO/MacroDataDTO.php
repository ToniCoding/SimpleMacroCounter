<?php

namespace src\DTO;

class MacroDataDTO
{
    public function __construct(
        public float $protein = 0,
        public float $carbs = 0,
        public float $fats = 0,
        public float $fiber = 0,
        public float $calories = 0
    ) {}

    public function getProtein(): float { return $this->protein; }
    public function setProtein(float $protein): void { $this->protein = $protein; }

    public function getCarbs(): float { return $this->carbs; }
    public function setCarbs(float $carbs): void { $this->carbs = $carbs; }

    public function getFats(): float { return $this->fats; }
    public function setFats(float $fats): void { $this->fats = $fats; }

    public function getFiber(): float { return $this->fiber; }
    public function setFiber(float $fiber): void { $this->fiber = $fiber; }

    public function getCalories(): float { return $this->calories; }
    public function setCalories(float $calories): void { $this->calories = $calories; }
}

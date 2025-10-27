<?php

namespace src\DTO;

class MacroDataDTO {
    public function __construct(
        public int $protein,
        public int $carbs,
        public int $fats,
        public int $fiber
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
}

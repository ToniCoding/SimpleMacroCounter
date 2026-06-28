<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class MacroSettingsDTO {
    public function __construct(
        #[Assert\NotNull]
        #[Assert\PositiveOrZero]
        private float $newProtein = 0,
        
        #[Assert\NotNull]
        #[Assert\PositiveOrZero]
        private float $newCarbs = 0,
        
        #[Assert\NotNull]
        #[Assert\PositiveOrZero]
        private float $newFats = 0,
        
        #[Assert\NotNull]
        #[Assert\PositiveOrZero]
        private float $newFiber = 0,
        
        #[Assert\NotNull]
        #[Assert\PositiveOrZero]
        private float $newCalories = 0
    ) {}

    public function getNewProtein(): int {
        return $this->newProtein;
    }
    public function setNewProtein(int $newProtein): void {
        $this->newProtein = $newProtein;
    }

    public function getNewCarbs(): int {
        return $this->newCarbs;
    }
    public function setNewCarbs(int $newCarbs): void {
        $this->newCarbs = $newCarbs;
    }

    public function getNewFats(): int {
        return $this->newFats;
    }
    public function setNewFats(int $newFats): void {
        $this->newFats = $newFats;
    }

    public function getNewFiber(): int {
        return $this->newFiber;
    }
    public function setNewFiber(int $newFiber): void {
        $this->newFiber = $newFiber;
    }

    public function getNewCalories(): int {
        return $this->newCalories;
    }
    public function setNewCalories(int $newCalories): void {
        $this->newCalories = $newCalories;
    }

    public function __toString() {
        return 'Calories: ' . $this->getNewCalories() .
        "\n\tProtein: " . $this->getNewProtein() .
        "\n\tCarbs: " . $this->getNewCarbs() .
        "\n\tFats: " . $this->getNewFats() .
        "\n\tFiber: " . $this->getNewFiber();
    }
}

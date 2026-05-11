<?php

namespace src\DTO;

class MacroSettingsDTO
{
    public function __construct(
        public int $newProtein = 0,
        public int $newCarbs = 0,
        public int $newFats = 0,
        public int $newFiber = 0,
        public int $newCalories = 0
    ) {}

    public function getNewProtein(): int { return $this->newProtein; }
    public function setNewProtein(int $newProtein): void { $this->newProtein = $newProtein; }

    public function getNewCarbs(): int { return $this->newCarbs; }
    public function setNewCarbs(int $newCarbs): void { $this->newCarbs = $newCarbs; }

    public function getNewFats(): int { return $this->newFats; }
    public function setNewFats(int $newFats): void { $this->newFats = $newFats; }

    public function getNewFiber(): int { return $this->newFiber; }
    public function setNewFiber(int $newFiber): void { $this->newFiber = $newFiber; }

    public function getNewCalories(): int { return $this->newCalories; }
    public function setNewCalories(int $newCalories): void { $this->newCalories = $newCalories; }
}

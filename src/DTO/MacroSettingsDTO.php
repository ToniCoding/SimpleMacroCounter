<?php

namespace src\DTO;

class MacroSettingsDTO {

    public function __construct(
        public int $newProtein = 0,
        public int $newCarbs = 0,
        public int $newFats = 0,
        public int $newFiber = 0,
        public int $newCalories = 0
    ) {}

    public function getNewProtein() {
        return $this->newProtein;
    }

    public function setNewProtein(int $newProtein) {
        $this->newProtein = $newProtein;
    }

    public function getNewCarbs() {
        return $this->newCarbs;
    }

    public function setNewCarbs(int $newCarbs) {
        $this->newCarbs = $newCarbs;
    }

    public function getNewFats() {
        return $this->newFats;
    }

    public function setNewFats(int $newFats) {
        $this->newFats = $newFats;
    }

    public function getNewFiber() {
        return $this->newFiber;
    }

    public function setNewFiber(int $newFiber) {
        $this->newFiber = $newFiber;
    }

    public function getNewCalories() {
        return $this->newCalories;
    }

    public function setNewCalories(int $newCalories) {
        $this->newCalories = $newCalories;
    }
}

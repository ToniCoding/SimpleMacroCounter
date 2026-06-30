<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class MacroDataDTO {
    public function __construct(
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
        private float $fiber = 0,

        #[Assert\NotNull]
        #[Assert\PositiveOrZero]
        private float $calories = 0,

        #[Assert\NotNull]
        private string $intent = ''
    ) {}

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

    public function getCalories(): float {
        return $this->calories;
    }

    public function setCalories(float $calories): void {
        $this->calories = $calories;
    }

    public function getIntent(): string {
        return $this->intent;
    }

    public function setIntent(string $intent): void {
        $this->intent = $intent;
    }

    public function __toArray(): array {
        return [
            "caloriesGoal" => $this->getCalories(),
            "proteinGoal" => $this->getProtein(),
            "carbGoal" => $this->getCarbs(),
            "fatGoal" => $this->getFats(),
            "fiberGoal" => $this->getFiber()
        ];
    }

    public function __toString() {
        return 'Calories: ' . $this->getCalories() .
        "\n\tProtein: " . $this->getProtein() .
        "\n\tCarbs: " . $this->getCarbs() .
        "\n\tFats: " . $this->getFats() .
        "\n\tFiber: " . $this->getFiber() .
        "\n\tIntent: " . $this->getIntent();
    }
}

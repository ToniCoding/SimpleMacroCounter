<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class MacroDataDTO {
    public function __construct(
        #[Assert\NotNull]
        #[Assert\PositiveOrZero]
        public readonly float $calories = 0,
        
        #[Assert\NotNull]
        #[Assert\PositiveOrZero]
        public readonly float $protein = 0,

        #[Assert\NotNull]
        #[Assert\PositiveOrZero]
        public readonly float $carbs = 0,

        #[Assert\NotNull]
        #[Assert\PositiveOrZero]
        public readonly float $fats = 0,

        #[Assert\NotNull]
        #[Assert\PositiveOrZero]
        public readonly float $fiber = 0,

        #[Assert\NotNull]
        public readonly string $intent = ''
    ) {}

    public function __toArray(): array {
        return [
            "caloriesGoal" => $this->calories,
            "proteinGoal" => $this->protein,
            "carbGoal" => $this->carbs,
            "fatGoal" => $this->fats,
            "fiberGoal" => $this->fiber
        ];
    }

    public function __toString(): string {
        return 'Calories: ' . $this->calories .
        "\n\tProtein: " . $this->protein .
        "\n\tCarbs: " . $this->carbs .
        "\n\tFats: " . $this->fats .
        "\n\tFiber: " . $this->fiber .
        "\n\tIntent: " . $this->intent;
    }
}

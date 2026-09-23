<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class UserGoalsDTO {
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
        public readonly float $fiber = 0
    ) {}

    public function __toArray(): array {
        return [
            'calories' => $this->calories,
            'protein' => $this->protein,
            'carbs' => $this->carbs,
            'fats' => $this->fats,
            'fiber' => $this->fiber
        ];
    }

    public function __toString(): string {
        return 'Calories: ' . $this->calories .
        "\n\tProtein: " . $this->protein .
        "\n\tCarbs: " . $this->carbs .
        "\n\tFats: " . $this->fats .
        "\n\tFiber: " . $this->fiber;
    }
}

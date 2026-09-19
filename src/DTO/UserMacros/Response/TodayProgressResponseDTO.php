<?php

namespace App\DTO\UserMacros\Response;

use Symfony\Component\Validator\Constraints as Assert;

class TodayProgressResponseDTO {
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Type('array')]
        public readonly array $todayMacrosProgress,

        #[Assert\NotBlank]
        #[Assert\Type('array')]
        public readonly array $todayUserMacroGrams,

        #[Assert\NotBlank]
        #[Assert\Type('array')]
        public readonly array $dailyMacroGramsGoal,

        #[Assert\NotBlank]
        #[Assert\Type('int')]
        #[Assert\PositiveOrZero]
        public readonly int $weeklyCalorieGoal,

        #[Assert\NotBlank]
        #[Assert\Type('int')]
        #[Assert\PositiveOrZero]
        public readonly int $weeklyCalorieConsumption,

        #[Assert\NotBlank]
        #[Assert\Type('array')]
        public readonly array $weeklyCalorieGoalRiskInfo,
    ) {}
}

<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class TodayProgressResponseDTO {
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Type('int')]
        #[Assert\PositiveOrZero]
        private int $todayCalories,

        #[Assert\NotBlank]
        #[Assert\Type('array')]
        private array $todayMacros,

        #[Assert\NotBlank]
        #[Assert\Type('int')]
        #[Assert\PositiveOrZero]
        private int $weeklyCalorieGoal,

        #[Assert\NotBlank]
        #[Assert\Type('array')]
        private array $weeklyMacroGoals,

        #[Assert\NotBlank]
        #[Assert\Type('int')]
        #[Assert\PositiveOrZero]
        private int $weeklyCalorieConsumption,

        #[Assert\NotBlank]
        #[Assert\Type('float')]
        private float $weeklyGoalRisk,

        #[Assert\NotBlank]
        #[Assert\Type('string')]
        #[Assert\AtLeastOneOf('gray, green, yellow, red')]
        private string $weeklyGoalRiskColor
    ) {}

    public function getTodayCalories(): int {
        return $this->todayCalories;
    }

    public function setTodayCalories(int $todayCalories): void {
        $this->todayCalories = $todayCalories;
    }

    public function getTodayMacros(): array {
        return $this->todayMacros;
    }

    public function setTodayMacros(array $todayMacros): void {
        $this->todayMacros = $todayMacros;
    }

    public function getWeeklyCalorieGoal(): int {
        return $this->weeklyCalorieGoal;
    }

    public function setWeeklyCalorieGoal(int $weeklyCalorieGoal): void {
        $this->weeklyCalorieGoal = $weeklyCalorieGoal;
    }

    public function getWeeklyMacroGoals(): array {
        return $this->weeklyMacroGoals;
    }

    public function setWeeklyMacroGoals(array $weeklyMacroGoals): void {
        $this->weeklyMacroGoals = $weeklyMacroGoals;
    }

    public function getWeeklyCalorieConsumption(): int {
        return $this->weeklyCalorieConsumption;
    }

    public function setWeeklyCalorieConsumption(int $weeklyCalorieConsumption): void {
        $this->weeklyCalorieConsumption = $weeklyCalorieConsumption;
    }

    public function getWeeklyGoalRisk(): float {
        return $this->weeklyGoalRisk;
    }

    public function setWeeklyGoalRisk(float $weeklyGoalRisk): void {
        $this->weeklyGoalRisk = $weeklyGoalRisk;
    }

    public function getWeeklyGoalRiskColor(): string {
        return $this->weeklyGoalRiskColor;
    }

    public function setWeeklyGoalRiskColor(string $weeklyGoalRiskColor): void {
        $this->weeklyGoalRiskColor = $weeklyGoalRiskColor;
    }
}
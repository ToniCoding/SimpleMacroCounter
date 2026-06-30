<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class TodayProgressResponseDTO implements \JsonSerializable {
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Type('array')]
        private array $todayMacrosProgress,

        #[Assert\NotBlank]
        #[Assert\Type('array')]
        private array $todayUserMacroGrams,

        #[Assert\NotBlank]
        #[Assert\Type('array')]
        private array $dailyMacroGramsGoal,

        #[Assert\NotBlank]
        #[Assert\Type('int')]
        #[Assert\PositiveOrZero]
        private int $weeklyCalorieGoal,

        #[Assert\NotBlank]
        #[Assert\Type('int')]
        #[Assert\PositiveOrZero]
        private int $weeklyCalorieConsumption,

        #[Assert\NotBlank]
        #[Assert\Type('array')]
        private array $weeklyCalorieGoalRiskInfo,
    ) {}

    public function getTodayMacrosProgress(): array {
        return $this->todayMacrosProgress;
    }

    public function setTodayMacrosProgress(array $todayMacrosProgress): void {
        $this->todayMacrosProgress = $todayMacrosProgress;
    }

    public function getDailyMacroGramsGoal(): array {
        return $this->dailyMacroGramsGoal;
    }

    public function setDailyMacroGramsGoal(array $dailyMacroGramsGoal): void {
        $this->dailyMacroGramsGoal = $dailyMacroGramsGoal;
    }

    public function getWeeklyCalorieGoal(): int {
        return $this->weeklyCalorieGoal;
    }

    public function setWeeklyCalorieGoal(int $weeklyCalorieGoal): void {
        $this->weeklyCalorieGoal = $weeklyCalorieGoal;
    }

    public function getTodayUserMacroGrams(): array {
        return $this->todayUserMacroGrams;
    }

    public function setTodayUserMacroGrams(array $todayUserMacroGrams): void {
        $this->todayUserMacroGrams = $todayUserMacroGrams;
    }

    public function getWeeklyCalorieConsumption(): int {
        return $this->weeklyCalorieConsumption;
    }

    public function setWeeklyCalorieConsumption(int $weeklyCalorieConsumption): void {
        $this->weeklyCalorieConsumption = $weeklyCalorieConsumption;
    }

    public function getWeeklyCalorieGoalRiskInfo(): array {
        return $this->weeklyCalorieGoalRiskInfo;
    }

    public function setWeeklyCalorieGoalRiskInfo(array $weeklyCalorieGoalRiskInfo): void {
        $this->weeklyCalorieGoalRiskInfo = $weeklyCalorieGoalRiskInfo;
    }

    public function jsonSerialize(): array {
        return [
            'todayMacrosProgress' => $this->getTodayMacrosProgress(),
            'todayUserMacroGrams' => $this->getTodayUserMacroGrams(),
            'dailyMacroGramsGoal' => $this->getDailyMacroGramsGoal(),
            'weeklyCalorieGoal' => $this->getWeeklyCalorieGoal(),
            'weeklyCalorieConsumption' => $this->getWeeklyCalorieConsumption(),
            'weeklyCalorieGoalRiskInfo' => $this->getWeeklyCalorieGoalRiskInfo()
        ];
    }
}
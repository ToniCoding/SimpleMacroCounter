<?php

namespace App\Service;

use App\DTO\MacroDataDTO;
use App\Entity\User;
use App\Exceptions\NoRecordFoundException;
use App\Repository\UserGoalsRepository;
use App\Service\DailyIntakeRecord;
use App\Repository\KcalsDailyRepository;
use App\Helpers\DateParser;

class UserMacrosRetrieve
{
    public function __construct(
        private DailyIntakeRecord $dailyIntakeRecord,
        private KcalsDailyRepository $kcalsDailyRepository,
        private UserGoalsRepository $userGoalsRepository,
        private DateParser $dateParser
    ) {}

    /**
     * Calculates the user progress and returns the progress based on their goals.
     * @param User $user
     * @return array
     */
    public function calculateUserProgress(User $user): array {
        $consumedMacros = $this->getConsumedMacros($user);
        $macroGoals = $this->getMacroGoals($user);

        $proteinConsumed = (float) $consumedMacros->getProtein();
        $carbsConsumed = (float) $consumedMacros->getCarbs();
        $fatsConsumed = (float) $consumedMacros->getFats();
        $fiberConsumed = (float) $consumedMacros->getFiber();
        $caloriesConsumed = (float) $consumedMacros->getCalories();

        $proteinGoal = (float) $macroGoals->getProtein();
        $carbsGoal = (float) $macroGoals->getCarbs();
        $fatsGoal = (float) $macroGoals->getFats();
        $fiberGoal = (float) $macroGoals->getFiber();
        $caloriesGoal = (float) $macroGoals->getCalories();

        return [
            'proteinProgress' => $proteinGoal !== 0.0 ? floor(($proteinConsumed / $proteinGoal) * 100) : 0,
            'carbProgress' => $carbsGoal !== 0.0 ? floor(($carbsConsumed / $carbsGoal) * 100) : 0,
            'fatProgress' => $fatsGoal !== 0.0 ? floor(($fatsConsumed / $fatsGoal) * 100) : 0,
            'fiberProgress' => $fiberGoal !== 0.0 ? floor(($fiberConsumed / $fiberGoal) * 100) : 0,
            'calorieProgress' => $caloriesGoal !== 0.0 ? floor(($caloriesConsumed / $caloriesGoal) * 100) : 0
        ];
    }

    public function getDataFromPreviousDays(User $user, int $previousDays): array {
        $dbData = $this->kcalsDailyRepository->findIntakeRegistryForDateRange($user, $previousDays);
        $historyData = [];

        foreach ($dbData as $dbRow) {
            $date = $dbRow->getDate()->format('Y-m-d');
            $historyData[$date][] = $dbRow;
        }

        return $historyData;
    }

    public function getConsumedMacros(User $user): MacroDataDTO {
        $consumedMacros = $this->dailyIntakeRecord->ensureDailyIntakeRecord($user);

        return new MacroDataDTO(
            (float) $consumedMacros->getProtein(),
            (float) $consumedMacros->getCarbs(),
            (float) $consumedMacros->getFats(),
            (float) $consumedMacros->getFiber(),
            (float) $consumedMacros->getKcals()
        );
    }

    public function getConsumedMacrosArr(User $user): array {
        $consumedMacros = $this->dailyIntakeRecord->ensureDailyIntakeRecord($user);

        return [
            'proteinGrams' => (float) $consumedMacros->getProtein(),
            'carbGrams' => (float) $consumedMacros->getCarbs(),
            'fatGrams' => (float) $consumedMacros->getFats(),
            'fiberGrams' => (float) $consumedMacros->getFiber(),
            'calories' => (float) $consumedMacros->getKcals()
        ];
    }

    public function getMacroGoals(User $user): MacroDataDTO {
        $userGoals = $this->dailyIntakeRecord->ensureOneMacroGoal($user);

        return new MacroDataDTO(
            (float) $userGoals->getProtein(),
            (float) $userGoals->getCarbs(),
            (float) $userGoals->getFats(),
            (float) $userGoals->getFiber(),
            (float) $userGoals->getCalories()
        );
    }

    public function getMacroGoalsArr(User $user): array {
        $userGoals = $this->dailyIntakeRecord->ensureOneMacroGoal($user);

        return [
            'proteinGoal' => (float) $userGoals->getProtein(),
            'carbGoal' => (float) $userGoals->getCarbs(),
            'fatGoal' => (float) $userGoals->getFats(),
            'fiberGoal' => (float) $userGoals->getFiber(),
            'caloriesGoal' => (float) $userGoals->getCalories()
        ];
    }

    public function getUserWeeklyGoal(User $user): int {
        $goalRegistry = $this->userGoalsRepository->findOneBy(['user' => $user]);

        if (!$goalRegistry) {
            throw new NoRecordFoundException();
        }

        return $goalRegistry->getCalories() * 7;
    }

    public function getCaloriesConsumedForCurrentWeek(User $user): int {
        $weekStart = new \DateTime('monday this week');
        $weekEnd = new \DateTime('sunday this week');

        $caloriesConsumed = $this->kcalsDailyRepository->findByDateRange($weekStart->setTime(0, 0, 0), $weekEnd->setTime(23, 59, 59), $user);
        $totalCalories = 0;

        foreach($caloriesConsumed as $item) {
            $totalCalories += $item->getKcals();
        }

        return $totalCalories;
    }

    /**
     * Calculates the risk of the user exceeding its weekly calorie goal based on
     * the remaining days. T
     * @param int $weeklyConsumption
     * @param int $weeklyGoal
     * @return bool
     */
    public function calculateWeeklyRisk(float $weeklyGoal, float $weeklyConsumption, float $todayConsumption = 0): array {
        $weights = [
            1 => 1.0,
            2 => 1.0,
            3 => 1.0,
            4 => 1.1,
            5 => 1.2,
            6 => 1.4,
            7 => 1.3,
        ];

        $today = new \DateTime('now');
        $currentDay = (int) $today->format('N');

        $daysPassed = $currentDay - 1;

        $remainingBudget = $weeklyGoal - ($weeklyConsumption + $todayConsumption);

        $daysPassed > 0
            ? $averageDaily = $weeklyConsumption / $daysPassed
            : $averageDaily = $weeklyGoal / 7;

        $expectedConsumption = 0;

        for ($day = $currentDay; $day <= 7; $day++) {
            $expectedConsumption += $weights[$day] * $averageDaily;
        }

        $remainingBudget <= 0
            ? $risk = 999
            : $risk = $expectedConsumption / $remainingBudget;

        $level = $this->getRiskLevel($risk);

        return [
            'risk' => round($risk, 2),
            'risk_color' => $this->getRiskColor($level),
            'level' => $level,
            'expected_consumption' => round($expectedConsumption),
            'remaining_budget' => round($remainingBudget),
        ];
    }

    private function getRiskLevel(float $risk): string {
        if ($risk < 0.8) {
            return 'low';
        }
        if ($risk < 1.0) {
            return 'medium';
        }
        if ($risk < 1.2) {
            return 'high';
        }
        return 'very_high';
    }

    private function getRiskColor(string $riskLevel): string {
        return match($riskLevel) {
            'low' => 'green',
            'medium' => 'yellow',
            'high', 'very_high' => 'red',
            default => 'gray',
        };
    }
}

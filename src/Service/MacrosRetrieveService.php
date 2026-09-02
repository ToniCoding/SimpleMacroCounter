<?php

namespace App\Service;

use App\Entity\User;
use App\Exceptions\NoRecordFoundException;
use App\Repository\{UserGoalsRepository, KcalsDailyRepository};
use Psr\Log\LoggerInterface;

class MacrosRetrieveService {
    public function __construct(
        private DailyIntakeRecordService $dailyIntakeRecordService,
        private KcalsDailyRepository $kcalsDailyRepository,
        private UserGoalsRepository $userGoalsRepository,
        private LoggerInterface $log
    ) {}

    /**
     * Calculates the user caloric and macro-nutient progress expressed with percentages.
     * @param User $user Owner of the information.
     * @return array{calorieProgress: float, carbProgress: float, fatProgress: float, fiberProgress: float, proteinProgress: float}
     */
    public function calculateUserProgress(User $user) {
        $macroGramsConsumed = $this->dailyIntakeRecordService->ensureDailyIntakeRecord($user)->__toArray();
        $macroIntakeGoal = $this->dailyIntakeRecordService->ensureOneMacroGoal($user)->__toArray();

        return $this->calculateProgressInPercentage($macroGramsConsumed, $macroIntakeGoal);
    }

    /**
     * Returns the data of calories and macro-nutrients between a date range.
     * @param User $user Owner of the information.
     * @param int $previousDays The number of days to go back since yesterday.
     * @return array[] Accessible data of the calories and macro-nutrients consumed in the date range.
     */
    public function getDataFromPreviousDays(User $user, int $previousDays): array {
        $dbData = $this->kcalsDailyRepository->findIntakeRegistryForDateRange($user, $previousDays);
        $historyData = [];

        foreach ($dbData as $dbRow) {
            $date = $dbRow->getDate()->format('Y-m-d');
            $historyData[$date][] = $dbRow;
        }

        return $historyData;
    }

    /**
     * Gets the calories consumed for the current week so far.
     * @param User $user Owner of the information.
     * @return int The number of calories consumed this week.
     */
    public function getCaloriesConsumedForThisWeek(User $user): int {
        $weekStart = new \DateTime('monday this week');
        $weekEnd = new \DateTime('sunday this week');

        $weekStart = $weekStart->setTime(0, 0, 0);
        $weekEnd = $weekEnd->setTime(23, 59, 59);

        $dailyCalorieConsumptionForTheWeek = $this->kcalsDailyRepository->findByDateRange($weekStart, $weekEnd, $user);
        $weeklyCalories = 0;

        foreach($dailyCalorieConsumptionForTheWeek as $dailyRegistry) {
            $weeklyCalories += $dailyRegistry->getKcals();
        }

        return $weeklyCalories;
    }

    public function getWeeklyCalorieGoal(User $user): int {
        $userGoalRecord = $this->userGoalsRepository->findOneBy(['user' => $user]);

        if (!$userGoalRecord) {
            throw new NoRecordFoundException();
        }

        return $userGoalRecord->getCalories() * 7;
    }

    /**
     * Does the math to calculate the percentages for the calories and macro-nutrients.
     * @param array $macroGramsConsumed Data about what the user consumed today.
     * @param array $macroGramsGoal Data about what are the user goals.
     * @return array{calorieProgress: float, carbProgress: float, fatProgress: float, fiberProgress: float, proteinProgress: float} The progress expressed in percentages.
     */
    private function calculateProgressInPercentage(array $macroGramsConsumed, array $macroGramsGoal) {
        $progressData = [];

        foreach ($macroGramsConsumed as $macroName => $macroValue) {
            $macroNameProgress = $macroName . 'Progress';

            if ($macroValue === 0.00) {
                $progressData[$macroNameProgress] = 0.00;
                continue;
            }

            $progressData[$macroNameProgress] = floor(($macroGramsConsumed[$macroName] / $macroGramsGoal[$macroName]) * 100);
        }

        return $progressData;
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

    /**
     * Determines how high is the risk based on the probability.
     * @param float $risk Calculated probability.
     * @return string The risk.
     */
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

    /**
     * Determines the risk color based on the level.
     * !!! TO BE DEPRECATED AFTER FURTHER ADOPTION OF THE API !!!
     * @param string $riskLevel The calculated risk level.
     * @return string The color based on the risk.
     */
    private function getRiskColor(string $riskLevel): string {
        return match($riskLevel) {
            'low' => 'green',
            'medium' => 'yellow',
            'high', 'very_high' => 'red',
            default => 'gray',
        };
    }
}

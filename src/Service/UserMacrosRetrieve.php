<?php

namespace src\Service;

use src\DTO\MacroDataDTO;
use src\Entity\User;
use src\Service\DailyIntakeRecord;
use src\Repository\KcalsDailyRepository;

class UserMacrosRetrieve
{
    public function __construct(
        private DailyIntakeRecord $dailyIntakeRecord,
        private KcalsDailyRepository $kcalsDailyRepository
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
}

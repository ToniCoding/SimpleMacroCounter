<?php

namespace src\Service;

use src\DTO\MacroDataDTO;
use src\Entity\User;
use src\Service\DailyIntakeRecord;
use src\Repository\KcalsDailyRepository;

use Doctrine\ORM\{EntityManagerInterface, EntityRepository};

class UserMacrosRetrieve
{
    private EntityRepository $userGoals;
    private EntityRepository $kcalsDaily;

    public function __construct(
        private EntityManagerInterface $entityManager,
        private DailyIntakeRecord $dailyIntakeRecord,
        private KcalsDailyRepository $kcalsDailyRepository
    ) {}

    public function calculateUserProgress(User $user): MacroDataDTO
    {
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

        return new MacroDataDTO(
            $proteinGoal !== 0.0 ? floor(($proteinConsumed / $proteinGoal) * 100) : 0,
            $carbsGoal !== 0.0 ? floor(($carbsConsumed / $carbsGoal) * 100) : 0,
            $fatsGoal !== 0.0 ? floor(($fatsConsumed / $fatsGoal) * 100) : 0,
            $fiberGoal !== 0.0 ? floor(($fiberConsumed / $fiberGoal) * 100) : 0,
            $caloriesGoal !== 0.0 ? floor(($caloriesConsumed / $caloriesGoal) * 100) : 0
        );
    }

    public function getDataFromPreviousDays(User $user, int $previousDays): array
    {
        $dbData = $this->kcalsDailyRepository->findIntakeRegistryForDateRange($user, $previousDays);
        $historyData = [];

        foreach ($dbData as $dbRow) {
            $date = $dbRow->getDate()->format('Y-m-d');
            $historyData[$date][] = $dbRow;
        }

        return $historyData;
    }

    public function getConsumedMacros(User $user): MacroDataDTO
    {
        $consumedMacros = $this->dailyIntakeRecord->ensureDailyIntakeRecord($user);

        return new MacroDataDTO(
            (float) $consumedMacros->getProtein(),
            (float) $consumedMacros->getCarbs(),
            (float) $consumedMacros->getFats(),
            (float) $consumedMacros->getFiber(),
            (float) $consumedMacros->getKcals()
        );
    }

    public function getMacroGoals(User $user): MacroDataDTO
    {
        $userGoals = $this->dailyIntakeRecord->ensureOneMacroGoal($user);

        return new MacroDataDTO(
            (float) $userGoals->getProtein(),
            (float) $userGoals->getCarbs(),
            (float) $userGoals->getFats(),
            (float) $userGoals->getFiber(),
            (float) $userGoals->getCalories()
        );
    }
}

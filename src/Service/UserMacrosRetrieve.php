<?php

namespace src\Service;

use src\DTO\MacroDataDTO;
use src\Entity\User;
use src\Service\DailyIntakeRecord;
use src\Repository\KcalsDailyRepository;

use Doctrine\ORM\{EntityManagerInterface, EntityRepository};

class UserMacrosRetrieve {
    private EntityRepository $userGoals;
    private EntityRepository $kcalsDaily;
    
    public function __construct(
        private EntityManagerInterface $entityManager,
        private DailyIntakeRecord $dailyIntakeRecord,
        private KcalsDailyRepository $kcalsDailyRepository
    ) {}

    public function calculateUserProgress(User $user): MacroDataDTO {
        $consumedMacros = $this->getConsumedMacros($user);
        $macroGoals = $this->getMacroGoals($user);

        $proteinConsumed = $consumedMacros->getProtein() ?: 0;
        $carbsConsumed = $consumedMacros->getCarbs() ?: 0;
        $fatsConsumed = $consumedMacros->getFats() ?: 0;
        $fiberConsumed = $consumedMacros->getFiber() ?: 0;
        $caloriesConsumed = $consumedMacros->getCalories() ?: 0;

        return new MacroDataDTO(
            $proteinConsumed != 0 ? floor($proteinConsumed / $macroGoals->getProtein() * 100) : 0,
            $carbsConsumed != 0 ? floor($carbsConsumed / $macroGoals->getCarbs() * 100) : 0,
            $fatsConsumed != 0 ? floor($fatsConsumed / $macroGoals->getFats() * 100) : 0,
            $fiberConsumed != 0 ? floor($fiberConsumed / $macroGoals->getFiber() * 100) : 0,
            $caloriesConsumed != 0 ? floor($caloriesConsumed / $macroGoals->getCalories() * 100) : 0
        );
    }

    public function getDataFromPreviousDays(User $user, int $previousDays): array {
        $dbData = $this->kcalsDailyRepository->findIntakeRegistryForDateRange($user, $previousDays);
        $historyData = [];

        foreach($dbData as $dbRow) {
            $date = $dbRow->getDate()->format('Y-m-d');
            $historyData[$date][] = $dbRow;
        }

        return $historyData;
    }

    private function getConsumedMacros(User $user): MacroDataDTO {
        $consumedMacros = $this->dailyIntakeRecord->ensureDailyIntakeRecord($user);

        return new MacroDataDTO(
            $consumedMacros->getProtein(),
            $consumedMacros->getCarbs(),
            $consumedMacros->getFats(),
            $consumedMacros->getFiber(),
            $consumedMacros->getKcals()
        );
    }

    private function getMacroGoals(User $user): MacroDataDTO {
        $userGoals = $this->dailyIntakeRecord->ensureOneMacroGoal($user);

        return new MacroDataDTO(
            $userGoals->getProtein(),
            $userGoals->getCarbs(),
            $userGoals->getFats(),
            $userGoals->getFiber(),
            $userGoals->getCalories()
        );
    }
}

<?php

namespace src\Service;

use src\DTO\MacroDataDTO;
use src\Entity\{User, UserGoals, KcalsDaily};
use src\Service\DailyIntakeRecord;
use src\Repository\KcalsDailyRepository;

use Doctrine\ORM\{EntityManagerInterface, EntityRepository};

class UserMacrosRetrieve {
    private EntityRepository $userGoals;
    private EntityRepository $kcalsDaily;
    
    public function __construct(
        private EntityManagerInterface $entityManager,
        private DailyIntakeRecord $dailyIntakeRecord
    ) {}

    public function calculateUserProgress(User $user): MacroDataDTO {
        $consumedMacros = $this->getConsumedMacros($user);
        $macroGoals = $this->getMacroGoals($user);

        $proteinConsumed = $consumedMacros->getProtein() ?: 0;
        $carbsConsumed = $consumedMacros->getCarbs() ?: 0;
        $fatsConsumed = $consumedMacros->getFats() ?: 0;
        $fiberConsumed = $consumedMacros->getFiber() ?: 0;

        return new MacroDataDTO(
            $proteinConsumed != 0 ? floor(($proteinConsumed / $macroGoals->getProtein()) * 100) : 0,
            $carbsConsumed != 0 ? floor(($carbsConsumed / $macroGoals->getCarbs()) * 100) : 0,
            $fatsConsumed != 0 ? floor(($fatsConsumed / $macroGoals->getFats()) * 100) : 0,
            $fiberConsumed != 0 ? floor(($fiberConsumed / $macroGoals->getFiber()) * 100) : 0
        );
    }

    private function getConsumedMacros(User $user): MacroDataDTO {
        $consumedMacros = $this->dailyIntakeRecord->ensureDailyIntakeRecord($user);

        return new MacroDataDTO(
            $consumedMacros->getProtein(),
            $consumedMacros->getCarbs(),
            $consumedMacros->getFats(),
            $consumedMacros->getFiber()
        );
    }

    private function getMacroGoals(User $user): MacroDataDTO {
        $userGoals = $this->dailyIntakeRecord->ensureOneMacroGoal($user);

        return new MacroDataDTO(
            $userGoals->getProtein(),
            $userGoals->getCarbs(),
            $userGoals->getFats(),
            $userGoals->getFiber()
        );
    }

}

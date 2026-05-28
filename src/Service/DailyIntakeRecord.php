<?php

namespace src\Service;

use src\DTO\MacroSettingsDTO;
use src\Exceptions\UnrecognizedMacroException;
use src\Exceptions\WriteToDatabaseException;
use src\Repository\{UserGoalsRepository, KcalsDailyRepository};
use src\Entity\{KcalsDaily, User, UserGoals};

/**
 * Sub-service with the methods needed to check if there is any registry for user intake and goals.
 */
class DailyIntakeRecord {
    public function __construct(
        private KcalsDailyRepository $kcalsDailyRepository,
        private UserGoalsRepository $userGoalsRepository
    ) {}

    /**
     * Checks if there is an intake registry for today. If none, a new registry is created.
     * 
     * @param User $user Find the specific registry for the user.
     * @throws WriteToDatabaseException If there is any error while creating the intake registry.
     * @return KcalsDaily Existing registry for the day.
     */
    public function ensureDailyIntakeRecord(User $user): KcalsDaily {
        $existingMacroRecord = $this->kcalsDailyRepository->findIntakeRegistryForToday($user);

        if ($existingMacroRecord) return $existingMacroRecord;

        $newMacroRecord = new KcalsDaily($user);

        $newMacroRecord->setKcals(0);
        $newMacroRecord->setProtein("0.00");
        $newMacroRecord->setCarbs("0.00");
        $newMacroRecord->setFats("0.00");
        $newMacroRecord->setFiber("0.00");

        if (!$this->kcalsDailyRepository->insertIntakeRegistry($newMacroRecord)) {
            throw new WriteToDatabaseException('There was an error inserting a new intake registry.');
        }

        $this->kcalsDailyRepository->insertIntakeRegistry($newMacroRecord);

        return $this->kcalsDailyRepository->findIntakeRegistryForToday($user);
    }

    /**
     * Checks if there is an intake goal registry for the user. If none, a new registry is created.
     * @param User $user Find the specific registry for the user.
     * @throws WriteToDatabaseException If there is any error while creating the intake registry.
     * @return UserGoals Existing registry for the user.
     */
    public function ensureOneMacroGoal(User $user): UserGoals {
        $existingMacroRecord = $this->userGoalsRepository->findGoalsRegistry($user);

        if ($existingMacroRecord) {
            return $existingMacroRecord;
        }

        $newGoalRegistry = new UserGoals($user, new \DateTime());

        $newGoalRegistry->setCalories(2000);
        $newGoalRegistry->setProtein("120.00");
        $newGoalRegistry->setCarbs("220.00");
        $newGoalRegistry->setFats("65.00");
        $newGoalRegistry->setFiber("30.00");

        $this->userGoalsRepository->insertGoalRegistry($newGoalRegistry);

        return $this->userGoalsRepository->findGoalsRegistry($user);
    }

    public function modifyMacroGoal(User $user, MacroSettingsDTO $macroSettingsDTO): void {
        $validatedMacroData = [];

        $macroUpdates = [
            'calories' => $macroSettingsDTO->getNewCalories(),
            'protein' => $macroSettingsDTO->getNewProtein(),
            'carbs' => $macroSettingsDTO->getNewCarbs(),
            'fats' => $macroSettingsDTO->getNewFats(),
            'fiber' => $macroSettingsDTO->getNewFiber()
        ];

        foreach ($macroUpdates as $macroName => $macroValue) {

            if ($macroValue === 0) continue;

            $macroMinimum = $this->getMinimumMacroValue($macroName);

            if ($macroMinimum === null) {
                throw new UnrecognizedMacroException();
            }

            if ($macroValue <= $macroMinimum) {
                $validatedMacroData[$macroName] = ($macroName === 'calories')
                    ? (int) $macroMinimum
                    : (string) $macroMinimum;
            } else {
                $validatedMacroData[$macroName] = ($macroName === 'calories')
                    ? (int) $macroValue
                    : (string) $macroValue;
            }
        }

        if (empty($validatedMacroData)) return;

        $this->userGoalsRepository->updateGoalRegistry($user, $validatedMacroData);
    }

    private function getMinimumMacroValue(string $macro): ?int {
        return match($macro) {
            'calories' => 1000,
            'protein' => 30,
            'carbs' => 50,
            'fats' => 10,
            'fiber' => 5,
            default => null
        };
    }
}

<?php

namespace src\Service;

use src\DTO\MacroSettingsDTO;
use src\Exceptions\UnrecognizedMacroException;
use src\Repository\{UserGoalsRepository, KcalsDailyRepository};
use src\Entity\{KcalsDaily, User, UserGoals};

class DailyIntakeRecord {
    public function __construct(
        private KcalsDailyRepository $kcalsDailyRepository,
        private UserGoalsRepository $userGoalsRepository
    ) {}

    public function ensureDailyIntakeRecord(User $user): ?KcalsDaily {
        $existing = $this->kcalsDailyRepository->findIntakeRegistryForToday($user);

        if ($existing) {
            return $existing;
        }

        $new = new KcalsDaily($user);

        $new->setKcals(0);
        $new->setProtein("0.00");
        $new->setCarbs("0.00");
        $new->setFats("0.00");
        $new->setFiber("0.00");

        try {
            $this->kcalsDailyRepository->insertIntakeRegistry($new);
        } catch (\Throwable $e) {
            return null;
        }

        return $this->kcalsDailyRepository->findIntakeRegistryForToday($user);
    }

    public function ensureOneMacroGoal(User $user): ?UserGoals {
        $existing = $this->userGoalsRepository->findGoalsRegistry($user);

        if ($existing) {
            return $existing;
        }

        $newGoalRegistry = new UserGoals($user, new \DateTime());

        $newGoalRegistry->setCalories(2000);
        $newGoalRegistry->setProtein("120.00");
        $newGoalRegistry->setCarbs("220.00");
        $newGoalRegistry->setFats("65.00");
        $newGoalRegistry->setFiber("30.00");

        try {
            $this->userGoalsRepository->insertGoalRegistry($newGoalRegistry);
        } catch (\Throwable $ex) {
            return null;
        }

        return $this->userGoalsRepository->findGoalsRegistry($user);
    }

    public function modifyMacroGoal(User $user, MacroSettingsDTO $macroSettingsDTO): void {
        try {
            $macroUpdates = [
                'calories' => $macroSettingsDTO->getNewCalories(),
                'protein' => $macroSettingsDTO->getNewProtein(),
                'carbs' => $macroSettingsDTO->getNewCarbs(),
                'fats' => $macroSettingsDTO->getNewFats(),
                'fiber' => $macroSettingsDTO->getNewFiber()
            ];

            $validatedMacroData = [];

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

        } catch (UnrecognizedMacroException $ex) {
            echo $ex;
        };
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

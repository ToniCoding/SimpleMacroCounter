<?php

namespace src\Service;

use src\DTO\MacroSettingsDTO;
use src\Exceptions\UnrecognizedMacroException;
use src\Repository\{UserGoalsRepository, KcalsDailyRepository};
use src\Entity\{KcalsDaily, User, UserGoals};

class DailyIntakeRecord {
    public function __construct(
        private KcalsDailyRepository $kcalsDailyRepository,
        private UserGoalsRepository $userGoalsRepository,
    ) {}

    public function ensureDailyIntakeRecord(User $user): ?KcalsDaily {
        if (!$this->kcalsDailyRepository->findIntakeRegistryForToday($user)) {
            $newKcalRegistry = new KcalsDaily($user);
            $newKcalRegistry->setKcals(0);
            $newKcalRegistry->setProtein(0);
            $newKcalRegistry->setCarbs(0);
            $newKcalRegistry->setFats(0);
            $newKcalRegistry->setFiber(0);

            try {
                $this->kcalsDailyRepository->insertIntakeRegistry($newKcalRegistry);
            } catch (\Exception $ex) {
                // ToDo: Log exception to file and throw specific exception.
                return null;
            }
        }

        return $this->kcalsDailyRepository->findIntakeRegistryForToday($user);
    }

    public function ensureOneMacroGoal(User $user): ?UserGoals {
        if (!$this->userGoalsRepository->findGoalsRegistry($user)) {
            // Those values must be constants at global level. Maybe even configurable through database for remote config.
            $newGoalRegistry = new UserGoals($user, new \DateTime());
            $newGoalRegistry->setCalories(2000);
            $newGoalRegistry->setProtein(120);
            $newGoalRegistry->setCarbs(220);
            $newGoalRegistry->setFats(65);
            $newGoalRegistry->setFiber(30);

            try {
                $this->userGoalsRepository->insertGoalRegistry($newGoalRegistry);
            } catch (\Exception $ex) {
                // ToDo: Log exception to file and throw specific exception.
                return null;
            }
        }

        return $this->userGoalsRepository->findGoalsRegistry($user);
    }

    public function modifyMacroGoal(User $user, MacroSettingsDTO $macroSettingsDTO) {
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
                $macroMinimum = $this->getMinimumMacroValue($macroName);

                if ($macroMinimum === null) {
                    throw new UnrecognizedMacroException();
                }

                if ($macroValue <= $macroMinimum) {
                    $validatedMacroData[$macroName] = $macroMinimum;
                } else {
                    $getterMethod = 'getNew' . ucfirst($macroName);
                    $validatedMacroData[$macroName] = $macroSettingsDTO->$getterMethod();
                }
            }

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

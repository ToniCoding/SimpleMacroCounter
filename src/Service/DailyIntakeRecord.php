<?php

namespace src\Service;

use src\Repository\{UserGoalsRepository, KcalsDailyRepository};
use src\Entity\{KcalsDaily, User, UserGoals};

class DailyIntakeRecord {
    public function __construct(
        private KcalsDailyRepository $kcalsDailyRepository,
        private UserGoalsRepository $userGoalsRepository
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
            $newGoalRegistry = new UserGoals($user, new \DateTime());
            $newGoalRegistry->setProtein(125);
            $newGoalRegistry->setCarbs(75);
            $newGoalRegistry->setFats(225);
            $newGoalRegistry->setFiber(35);

            try {
                $this->userGoalsRepository->insertGoalRegistry($newGoalRegistry);
            } catch (\Exception $ex) {
                // ToDo: Log exception to file and throw specific exception.
                return null;
            }
        }

        return $this->userGoalsRepository->findGoalsRegistry($user);
    }
}

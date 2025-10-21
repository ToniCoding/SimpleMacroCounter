<?php

namespace src\Service;

use Doctrine\ORM\{EntityManagerInterface, EntityRepository};
use src\Entity\KcalsDaily;
use src\Entity\User;
use src\Entity\UserGoals;

class DailyIntakeRecord {
    private EntityRepository $kcalsDailyRepository;
    private EntityRepository $userGoalsRepository;

    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
        $this->kcalsDailyRepository = $entityManager->getRepository(KcalsDaily::class);   
        $this->userGoalsRepository = $entityManager->getRepository(UserGoals::class);
    }

    public function ensureDailyIntakeRecord(User $user) {
        if (!$this->kcalsDailyRepository->findOneBy([
            'user' => $user
        ])) {
            $newKcalRegistry = new KcalsDaily($user);
            $newKcalRegistry->setKcals(0);
            $newKcalRegistry->setProtein(0);
            $newKcalRegistry->setCarbs(0);
            $newKcalRegistry->setFats(0);

            try {
                $this->entityManager->persist($newKcalRegistry);
                $this->entityManager->flush();

                return true;
            } catch (\Exception $ex) {
                // ToDo: Log exception to file and throw specific exception.
                return false;
            }
        }

        return true;
    }

    public function ensureOneMacroGoal(User $user): bool {
        if (!$this->userGoalsRepository->findOneBy([
            'user' => $user
        ])) {
            $newGoalRegistry = new UserGoals($user, new \DateTime());
            $newGoalRegistry->setProtein(125);
            $newGoalRegistry->setCarbs(75);
            $newGoalRegistry->setFats(225);

            try {
                $this->entityManager->persist($newGoalRegistry);
                $this->entityManager->flush();
                
                return true;
            } catch (\Exception $ex) {
                // ToDo: Log exception to file and throw specific exception.
                return false;
            }
        }

        return true;
    }
}

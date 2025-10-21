<?php

namespace src\Service;

use src\DTO\MacroDataDTO;
use src\Entity\{User, UserGoals, KcalsDaily};
use src\Service\DailyIntakeRecord;
use src\Exceptions\NoRecordFoundException;

use Doctrine\ORM\{EntityManagerInterface, EntityRepository};

class UserMacrosRetrieve {
    private EntityRepository $userGoals;
    private EntityRepository $kcalsDaily;

    public function __construct(private EntityManagerInterface $entityManager, private DailyIntakeRecord $dailyIntakeRecord) {
        $this->userGoals = $this->entityManager->getRepository(UserGoals::class);
        $this->kcalsDaily = $this->entityManager->getRepository(KcalsDaily::class);
    }

    public function calculateUserProgress(User $user) {
        $consumedMacros = $this->getConsumedMacros($user);
        $macroGoals = $this->getMacroGoals($user);

        return new MacroDataDTO(
            floor(($consumedMacros->getProtein() / $macroGoals->getProtein()) * 100),
            floor(($consumedMacros->getCarbs() / $macroGoals->getCarbs()) * 100),
            floor(($consumedMacros->getFats() / $macroGoals->getFats()) * 100)
        );
    }

    private function getConsumedMacros(User $user): MacroDataDTO {
        $today = new \DateTimeImmutable('today');
        $tomorrow = $today->modify('+1 day');

        $consumed = $this->kcalsDaily->createQueryBuilder('kcals')
            ->where('kcals.user = :user')
            ->andWhere('kcals.date >= :today')
            ->andWhere('kcals.date < :tomorrow')
            ->setParameter('user', $user)
            ->setParameter('today', $today)
            ->setParameter('tomorrow', $tomorrow)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$consumed) {
            $this->dailyIntakeRecord->ensureDailyIntakeRecord($user);

            $consumed = $this->kcalsDaily->createQueryBuilder('kcals')
                ->where('kcals.user = :user')
                ->andWhere('kcals.date >= :today')
                ->andWhere('kcals.date < :tomorrow')
                ->setParameter('user', $user)
                ->setParameter('today', $today)
                ->setParameter('tomorrow', $tomorrow)
                ->getQuery()
                ->getOneOrNullResult();
        }

        return new MacroDataDTO(
            $consumed->getProtein(),
            $consumed->getCarbs(),
            $consumed->getFats()
        );
    }

    private function getMacroGoals(User $user): MacroDataDTO {
        $goals = $this->userGoals->findOneBy([
            'user' => $user
        ]);

        if (!$goals) {
            $this->dailyIntakeRecord->ensureOneMacroGoal($user);
            $goals = $this->userGoals->findOneBy([
                'user' => $user
            ]);
        }

        return new MacroDataDTO(
            $goals->getProtein(),
            $goals->getCarbs(),
            $goals->getFats()
        );
    }

}

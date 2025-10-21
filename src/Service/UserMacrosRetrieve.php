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
            $consumed->getFats(),
            $consumed->getFiber()
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
            $goals->getFats(),
            $goals->getFiber()
        );
    }

}

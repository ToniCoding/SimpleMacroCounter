<?php

namespace src\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

use src\Entity\{User, KcalsDaily};
use src\DTO\MacroDataDTO;

class KcalsDailyRepository extends ServiceEntityRepository
{
    private EntityManagerInterface $entityManagerInterface;

    public function __construct(
        ManagerRegistry $registry,
        EntityManagerInterface $entityManagerInterface,
    ) {
        parent::__construct($registry, KcalsDaily::class);
        $this->entityManagerInterface = $entityManagerInterface;
    }

    public function findIntakeRegistryForToday(User $user): ?KcalsDaily
    {
        $today = new \DateTimeImmutable('today');
        $tomorrow = $today->modify('+1 day');

        return $this->createQueryBuilder('kcals')
            ->where('kcals.user = :user')
            ->andWhere('kcals.date >= :today')
            ->andWhere('kcals.date < :tomorrow')
            ->setParameter('user', $user)
            ->setParameter('today', $today)
            ->setParameter('tomorrow', $tomorrow)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findIntakeRegistryForDateRange(User $user, int $previousDays): array
    {
        $yesterday = new \DateTimeImmutable('-1 day');
        $from = $yesterday->modify("-$previousDays days");

        return $this->createQueryBuilder('kcals')
            ->where('kcals.user = :user')
            ->andWhere('kcals.date >= :from')
            ->andWhere('kcals.date <= :to')
            ->setParameter('user', $user)
            ->setParameter('from', $from)
            ->setParameter('to', $yesterday)
            ->orderBy('kcals.date', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function insertIntakeRegistry(KcalsDaily $kcalsDailyEntity): bool
    {
        try {
            $this->entityManagerInterface->persist($kcalsDailyEntity);
            $this->entityManagerInterface->flush();
        } catch (\Exception $ex) {
            return false;
        }

        return true;
    }

    public function updateMacroIntake(User $user, MacroDataDTO $macroDataDTO, string $intent = ''): bool
    {
        $todaysIntakeRegistry = $this->findIntakeRegistryForToday($user);

        if (!$todaysIntakeRegistry) {
            return false;
        }

        $protein = (float) $todaysIntakeRegistry->getProtein();
        $carbs = (float) $todaysIntakeRegistry->getCarbs();
        $fats = (float) $todaysIntakeRegistry->getFats();
        $fiber = (float) $todaysIntakeRegistry->getFiber();
        $kcals = (int) $todaysIntakeRegistry->getKcals();

        $p = (float) $macroDataDTO->getProtein();
        $c = (float) $macroDataDTO->getCarbs();
        $f = (float) $macroDataDTO->getFats();
        $fi = (float) $macroDataDTO->getFiber();
        $k = (float) $macroDataDTO->getCalories();

        if ($intent === 'reduce') {
            $protein -= $p;
            $carbs -= $c;
            $fats -= $f;
            $fiber -= $fi;
            $kcals -= $k;
        } else {
            $protein += $p;
            $carbs += $c;
            $fats += $f;
            $fiber += $fi;
            $kcals += $k;
        }

        $todaysIntakeRegistry->setProtein((string) round($protein, 2));
$todaysIntakeRegistry->setCarbs((string) round($carbs, 2));
$todaysIntakeRegistry->setFats((string) round($fats, 2));
$todaysIntakeRegistry->setFiber((string) round($fiber, 2));
        $todaysIntakeRegistry->setKcals((int) $kcals);

        try {
            $this->entityManagerInterface->flush();
        } catch (\Exception $ex) {
            return false;
        }

        return true;
    }
}

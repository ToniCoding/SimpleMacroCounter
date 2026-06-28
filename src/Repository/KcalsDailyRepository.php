<?php

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

use App\Entity\{User, KcalsDaily};
use App\DTO\MacroDataDTO;
use Psr\Log\LoggerInterface;

class KcalsDailyRepository extends ServiceEntityRepository {
    public function __construct(
        private ManagerRegistry $registry,
        private LoggerInterface $logger
    ) {
        parent::__construct($registry, KcalsDaily::class);
    }

    public function findIntakeRegistryForToday(User $user): ?KcalsDaily {
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

    public function findIntakeRegistryForDateRange(User $user, int $previousDays): array {
        $yesterday = new \DateTimeImmutable('yesterday midnight');
        $from = $yesterday->modify("-$previousDays days");
        $to = $yesterday->modify('+1 day');

        return $this->createQueryBuilder('kcals')
            ->where('kcals.user = :user')
            ->andWhere('kcals.date >= :from')
            ->andWhere('kcals.date < :to')
            ->setParameter('user', $user)
            ->setParameter('from', $from)
            ->setParameter('to', $to)
            ->orderBy('kcals.date', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function insertIntakeRegistry(KcalsDaily $kcalsDailyEntity): bool {
        $this->getEntityManager()->persist($kcalsDailyEntity);
        $this->getEntityManager()->flush();
        
        return true;
    }

    public function updateMacroIntake(User $user, MacroDataDTO $macroDataDTO, string $intent = ''): bool {
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
            $this->getEntityManager()->flush();
        } catch (\Exception $ex) {
            return false;
        }

        return true;
    }

    public function findByDateRange(\DateTime $start, \DateTime $end, User $user): array {
        return $this->createQueryBuilder('k')
            ->where('k.date BETWEEN :start AND :end')
            ->andWhere('k.user = :user')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }
}

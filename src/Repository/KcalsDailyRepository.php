<?php

namespace src\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use src\Entity\{User, KcalsDaily};

class KcalsDailyRepository extends ServiceEntityRepository {
    private EntityManagerInterface $entityManagerInterface;

    public function __construct(
        ManagerRegistry $registry,
        EntityManagerInterface $entityManagerInterface
    ) {
        parent::__construct($registry, KcalsDaily::class);
        $this->entityManagerInterface = $entityManagerInterface;
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
    
    public function insertIntakeRegistry(KcalsDaily $kcalsDailyEntity): bool {
        try {
            $this->entityManagerInterface->persist($kcalsDailyEntity);
            $this->entityManagerInterface->flush();
        } catch (\Exception $ex) {
            return false;
        }

        return true;
    }
}

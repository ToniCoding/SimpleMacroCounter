<?php

namespace src\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use src\Entity\{User, KcalsDaily};
use src\DTO\MacroDataDTO;

use Psr\Log\LoggerInterface;

class KcalsDailyRepository extends ServiceEntityRepository {
    private EntityManagerInterface $entityManagerInterface;

    public function __construct(
        ManagerRegistry $registry,
        EntityManagerInterface $entityManagerInterface,
        private LoggerInterface $logger
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

    public function updateMacroIntake(User $user, MacroDataDTO $macroDataDTO): bool {
        $todaysIntakeRegistry = $this->findIntakeRegistryForToday($user);

        $this->logger->error($todaysIntakeRegistry->getProtein());
        $this->logger->error($todaysIntakeRegistry->getCarbs());
        $this->logger->error($todaysIntakeRegistry->getFats());
        $this->logger->error($todaysIntakeRegistry->getFiber());

        $this->logger->error($macroDataDTO->getProtein());
        $this->logger->error($macroDataDTO->getCarbs());
        $this->logger->error($macroDataDTO->getFats());
        $this->logger->error($macroDataDTO->getFiber());

        $todaysIntakeRegistry->setProtein($todaysIntakeRegistry->getProtein() + $macroDataDTO->getProtein());
        $todaysIntakeRegistry->setCarbs($todaysIntakeRegistry->getCarbs() + $macroDataDTO->getCarbs());
        $todaysIntakeRegistry->setFats($todaysIntakeRegistry->getFats() + $macroDataDTO->getFats());
        $todaysIntakeRegistry->setFiber($todaysIntakeRegistry->getFiber() + $macroDataDTO->getFiber());

        $this->logger->error($todaysIntakeRegistry->getProtein());
        $this->logger->error($todaysIntakeRegistry->getCarbs());
        $this->logger->error($todaysIntakeRegistry->getFats());
        $this->logger->error($todaysIntakeRegistry->getFiber());

        try {
            $this->entityManagerInterface->flush();
        } catch (\Exception $ex) {
            return false;
        }

        return true;
    }
}

<?php

namespace src\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use src\Entity\{User, UserGoals};

class UserGoalsRepository extends ServiceEntityRepository {
    private EntityManagerInterface $entityManagerInterface;

    public function __construct(
        private ManagerRegistry $registry,
        EntityManagerInterface $entityManagerInterface
    ) {
        parent::__construct($registry, UserGoals::class);
        $this->entityManagerInterface = $entityManagerInterface;
    }

    public function findGoalsRegistry(User $user): ?UserGoals {
        return $this->findOneBy([
            'user' => $user
        ]);
    }

    public function insertGoalRegistry(UserGoals $userGoals): bool {
        try {
            $this->entityManagerInterface->persist($userGoals);
            $this->entityManagerInterface->flush();
        } catch (\Exception $ex) {
            echo $ex;
            return false;
        }

        return true;
    }
}

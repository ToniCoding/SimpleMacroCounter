<?php

namespace src\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use src\Entity\{User, UserGoals};

class UserGoalsRepository extends ServiceEntityRepository {
    public function __construct(
        private ManagerRegistry $registry
    ) {
        parent::__construct($registry, UserGoals::class);
    }

    public function findGoalsRegistry(User $user): ?UserGoals {
        return $this->findOneBy([
            'user' => $user
        ]);
    }

    public function insertGoalRegistry(UserGoals $userGoals): bool {
        $this->getEntityManager()->persist($userGoals);
        $this->getEntityManager()->flush();

        return true;
    }

    public function updateGoalRegistry(User $user, array $newUserGoals): bool {
        $userGoals = $this->findGoalsRegistry($user);

        if (!$userGoals) {
            return false;
        }

        foreach ($newUserGoals as $macro => $value) {
            $setter = 'set' . ucfirst($macro);

            if (method_exists($userGoals, $setter)) {
                $userGoals->$setter($value);
            }
        }
        
        $this->getEntityManager()->flush();

        return true;
    }
}

<?php

namespace src\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use src\DTO\MacroSettingsDTO;
use src\Entity\{User, UserGoals};

// ToDo: This repo needs some work.
// Instead of using the raw entity, use a DTO.
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
        
        $this->entityManagerInterface->flush();

        return true;
    }
}

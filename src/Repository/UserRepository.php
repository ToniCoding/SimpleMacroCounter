<?php

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\User;

class UserRepository extends ServiceEntityRepository {
    public function __construct(
        private ManagerRegistry $registry
    ) {
        parent::__construct($registry, User::class);
    }

    /**
     * Checks if an user exists based on an email.
     * @param string $username The username to be checked.
     * @return bool True if the user already exists, false if not.
     */
    public function checkIfUserExistsByUsername(string $username): bool {
        return $this->findOneBy(['username' => $username]) !== null;
    }
}

<?php

namespace src\Handlers;

use src\DTO\{RegisterUserDTO, LoggedUserDTO};
use src\Entity\User;
use src\Exceptions\AgeNotAllowedException;
use src\Security\AccessTokenHandler;

use Doctrine\ORM\EntityManagerInterface;

class UserHandler {
    public function __construct(private EntityManagerInterface $entityManager, private AccessTokenHandler $accessTokenHandler) {}

    public function handle(string $action, RegisterUserDTO $registeredUserDTO = null, LoggedUserDTO $loggedUserDTO = null): bool {
        switch($action) {
            case 'register':
                $user = new User(new \DateTimeImmutable(), true);

                if (!($registeredUserDTO->getAge() >= 15 && $registeredUserDTO->getAge() <= 100)) throw new AgeNotAllowedException("You must be over 15 or under 100 years old.");

                $user->setUsername($registeredUserDTO->getUsername());
                $user->setPassword(password_hash($registeredUserDTO->getPassword(), PASSWORD_BCRYPT));
                $user->setEmail($registeredUserDTO->getEmail());
                $user->setUserAlias($registeredUserDTO->getAlias());
                $user->setAge($registeredUserDTO->getAge());
                $user->setLastLogin(new \DateTime);
                $user->setTimezone('Europe/Madrid');

                $this->entityManager->persist($user);
                $this->entityManager->flush();

                return true;
                
            case 'login':
                $username = $loggedUserDTO->getUsername();
                $password = $loggedUserDTO->getPassword();

                $userRepo = $this->entityManager->getRepository(User::class);
                $queryResult = $userRepo->createQueryBuilder('user')
                    -> select('user.username', 'user.password')
                    -> where('user.username = :username')
                    -> setParameter('username', $username)
                    -> getQuery()
                    -> getOneOrNullResult();
                    
                if ($queryResult) {
                    return password_verify($password, $queryResult['password']);
                }

                return false;
                
            default:
                return false;
        }
    }
}

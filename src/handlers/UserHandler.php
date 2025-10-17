<?php

namespace App\Handlers;

use App\DTO\{RegisterUserDTO, LoggedUserDTO};
use App\Entity\User;

use App\Security\AccessTokenHandler;
use Doctrine\ORM\EntityManagerInterface;

class UserHandler {
    public function __construct(private EntityManagerInterface $entityManager, private AccessTokenHandler $accessTokenHandler) {}

    public function handle(string $action, RegisterUserDTO $registeredUserDTO = null, LoggedUserDTO $loggedUserDTO = null): bool {
        switch($action) {
            case 'register':
                $user = new User(new \DateTimeImmutable(), true);

                $user->setUsername($registeredUserDTO->getUsername());
                $user->setPassword(password_hash($registeredUserDTO->getPassword(), PASSWORD_BCRYPT));
                $user->setEmail($registeredUserDTO->getEmail());
                $user->setUserAlias($registeredUserDTO->getAlias());
                $user->setAge($registeredUserDTO->getAge());
                $user->setLastLogin(new \DateTime);

                try {
                    $this->entityManager->persist($user);
                    $this->entityManager->flush();
                } catch (\Exception $ex) {
                    echo 'Exception thrown while trying to persist and flush a Doctrine entity user.';
                    echo $ex;
                    return false;
                }

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

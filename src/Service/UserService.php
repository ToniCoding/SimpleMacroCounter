<?php

namespace App\Service;

use App\DTO\UserData\Request\UserRegisterRequestDTO;
use App\Entity\User;
use App\Exceptions\{AlreadyRegisteredUsernameException, InvalidEmailProviderException};
use App\Repository\{UserRepository};
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserService {
    public function __construct(
        private UserRepository $userRepository,
        private UserPasswordHasherInterface $userPasswordHasherInterface,
        private ParameterBagInterface $params,
        private EntityManagerInterface $entityManagerInterface,
        private LoggerInterface $log
    ) {}

    /**
     * The method in charge of verifying and persisting any new user.
     * @param UserRegisterRequestDTO $userRegisterRequestDTO The DTO from the API call.
     * @throws AlreadyRegisteredUsernameException If the username is already registered in the database.
     * @throws InvalidEmailProviderException If the email provider is not valid.
     * @return User The newly created user entity.
     */
    public function register(UserRegisterRequestDTO $userRegisterRequestDTO): User {
        $registeredUser = new User();

        $username = $userRegisterRequestDTO->username;

        if ($this->userRepository->checkIfUserExistsByUsername($username)) {
            $this->log->error("[USER_SERVICE] User tried registering $username but is already registered.");
            throw new AlreadyRegisteredUsernameException();
        }

        if (!$this->verifyEmailBannedDomains($userRegisterRequestDTO->email)) {
            $this->log->error("[USER_SERVICE] User tried to use an invalid email provider.");
            throw new InvalidEmailProviderException();
        }

        $hashedPassword = $this->userPasswordHasherInterface->hashPassword($registeredUser, $userRegisterRequestDTO->password);
        
        $registeredUser->setUsername($username);
        $registeredUser->setPassword($hashedPassword);
        $registeredUser->setRoles(['ROLE_USER']);
        $registeredUser->setEmail($userRegisterRequestDTO->email);
        $registeredUser->setAge($userRegisterRequestDTO->age);
        $registeredUser->setTimezone('Europe/Madrid');

        $this->entityManagerInterface->persist($registeredUser);
        $this->entityManagerInterface->flush();

        return $registeredUser;
    }

    private function verifyEmailBannedDomains(string $email): bool {
        $validDomains = $this->params->get('user.valid_email_domains');
        $emailDomain = strtolower(substr(strstr($email, '@'), 1));

        return \in_array($emailDomain, $validDomains, true); 
    }
}

<?php

namespace App\Security;

use App\Entity\AccessToken;

use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\AccessToken\AccessTokenHandlerInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;

class AccessTokenHandler implements AccessTokenHandlerInterface {
    public function __construct(private EntityManagerInterface $entityManager) {}

    /**
     * Checks if the user has any active access token in database.
     * @param string $accessToken
     * @throws BadCredentialsException
     * @return UserBadge
     */
    public function getUserBadgeFrom(string $username): UserBadge {
        $tokenRepository = $this->entityManager->getRepository(AccessToken::class);
        $accessToken = $tokenRepository->findOneByValue($username);

        if ($accessToken === null || !$accessToken->isValid()) {
            throw new BadCredentialsException('Invalid credentials.');
        }

        return new UserBadge($accessToken);
    }

    /**
     * Create and sets an user badge in database.
     * @param UserInterface $user
     * @throws \Exception
     * @return bool
     */
    public function setUserBadgeIn(UserInterface $user): AccessToken {      
        if ($user === null) {
            throw new \Exception('User can not be null.');
        }

        $expiresAt = new \DateTimeImmutable();
        $accessToken = new AccessToken();
        $accessToken->setValue(bin2hex(random_bytes(32)));
        $accessToken->setExpiresAt($expiresAt->modify('+1 week'));
        $accessToken->setUser($user);

        $this->entityManager->persist($accessToken);
        $this->entityManager->flush();

        return $accessToken;
    }
}

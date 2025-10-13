<?php

namespace App\Security;

use App\Entity\AccessToken;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Http\AccessToken\AccessTokenHandlerInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;

//class AccessTokenHandler implements AccessTokenHandlerInterface {
//    public function __construct(private AccessToken $accessTokenRepository) {}

//    public function getUserBadgeFrom(string $accessToken): UserBadge {
//        $accessToken = $this->accessTokenRepository->findOneByValue($accessToken);
//
//        if ($accessToken === null || !$accessToken->isValid()) {}
//    }
//}

class AccessTokenHandler {
    public function __construct(){}
}

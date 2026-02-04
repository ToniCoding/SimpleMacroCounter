<?php

namespace src\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;

class AppAuthenticator extends AbstractLoginFormAuthenticator
{
    public const LOGIN_ROUTE = 'login';

    public function __construct(private RouterInterface $router) {}

    public function authenticate(Request $request): Passport {
        $username = $request->request->get('username', '');
        $password = $request->request->get('password', '');

        return new Passport(
            new UserBadge($username),
            new PasswordCredentials($password),
            [new CsrfTokenBadge('authenticate', $request->get('_csrf_token'))]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?RedirectResponse {
        return new RedirectResponse($this->router->generate('login_success'));
    }

    protected function getLoginUrl(Request $request): string {
        return $this->router->generate(self::LOGIN_ROUTE);
    }
}

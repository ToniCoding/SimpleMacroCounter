<?php

namespace App\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

#[AsEventListener(event: 'kernel.request')]
class ApiRateLimiterListener {
    public function __construct(
        private RateLimiterFactory $apiUserLimiter,
        private RateLimiterFactory $apiIpLimiter,
        private TokenStorageInterface $tokenStorage
    ) {}

    public function onKernelRequest(RequestEvent $event): void {
        $request = $event->getRequest();

        if (!str_starts_with($request->getPathInfo(), '/api/')) {
            return;
        }

        $token = $this->tokenStorage->getToken();
        $user = $token?->getUser();

        $limiter = $user 
            ? $this->apiUserLimiter->create($user->getUserIdentifier()) 
            : $this->apiIpLimiter->create($request->getClientIp());

        $limit = $limiter->consume(1);

        if (false === $limit->isAccepted()) {
            $response = new JsonResponse([
                'error' => 'Too many requests, please try again later.'
            ], 429);

            $response->headers->set('X-RateLimit-Limit', $limit->getLimit());
            $response->headers->set('X-RateLimit-Remaining', $limit->getRemainingTokens());
            $response->headers->set('Retry-After', $limit->getRetryAfter()->getTimestamp());

            $event->setResponse($response);
        }
    }
}

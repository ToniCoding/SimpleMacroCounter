<?php

namespace App\EventListeners;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Event listener responsible for applying rate limiting to incoming API requests 
 * based on authenticated user identifiers or client IP addresses.
 */
#[AsEventListener(event: 'kernel.request')]
class ApiRateLimiterListener {
    
    /**
     * Initializes the listener with rate limiter factories and the token storage service.
     * 
     * @param RateLimiterFactory $apiUserLimiter Factory to generate user-specific rate limiters.
     * @param RateLimiterFactory $apiIpLimiter Factory to generate IP-specific rate limiters.
     * @param TokenStorageInterface $tokenStorage Service to access the current authentication token.
     */
    public function __construct(
        private RateLimiterFactory $apiUserLimiter,
        private RateLimiterFactory $apiIpLimiter,
        private TokenStorageInterface $tokenStorage
    ) {}

    /**
     * Intercepts kernel requests to enforce rate limits on API endpoints.
     * 
     * @param RequestEvent $event The incoming request event.
     */
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

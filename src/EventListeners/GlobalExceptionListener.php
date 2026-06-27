<?php

namespace src\EventListeners;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\{RequestStack, RedirectResponse};
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Psr\Log\LoggerInterface;

#[AsEventListener(event: 'kernel.exception', priority: -10)]
class GlobalExceptionListener {
    public function __construct (
        private UrlGeneratorInterface $urlGenerator,
        private RequestStack $requestStack,
        private LoggerInterface $logger,
        private string $environment,
    ) {}

    /**
     * Manages the exception thrown and acts in consequence to its context.
     * @param ExceptionEvent $event The event of the exception being thrown.
     * @return void
     */
    // The handler needs some improvements for better legibility and escalable exception handling.
    public function onKernelException(ExceptionEvent $event): void {
        if ($event->hasResponse()) {
            return;
        }
        
        $exception = $event->getThrowable();
        $request = $event->getRequest();
        
        // if ($this->environment === 'dev') {
        //     $this->logger->debug('[GLOBAL_HANDLER] Showing exception: ' . $exception::class);
        //     return;
        // }

        if ($exception instanceof \Symfony\Component\Security\Core\Exception\AccessDeniedException ||
            $exception instanceof \Symfony\Component\Security\Core\Exception\AuthenticationException) {
            
            $this->logger->error('[GLOBAL_HANDLER] Security exception: ' . $exception->getMessage());
            
            try {
                $session = $this->requestStack->getSession();
                $session->set('security_error', 'Please log in to continue.');
            } catch (\Exception $e) {}
            
            $url = $this->urlGenerator->generate('app_login');
            $event->setResponse(new RedirectResponse($url));

            return;
        }
        
        if ($exception instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
            $this->logger->error('[GLOBAL_HANDLER] 404: ' . $request->getPathInfo());
            return;
        }
        
        if ($exception instanceof \src\Exceptions\FoodAlreadyRegistered) {
            $this->logger->error('[GLOBAL_HANDLER] The user tried to register a food that was previously registered.');

            $session = $this->requestStack->getSession();
            $session->set('registeringFoodError', $exception->getMessage());

            $event->setResponse(new RedirectResponse('/foods'));

            return;
        }

        $url = $this->urlGenerator->generate('app_error');
        $event->setResponse(new RedirectResponse($url));
        
        try {
            $session = $this->requestStack->getSession();
            $session->set('last_exception', [
                'user_message' => $this->getUserMessage($exception),
                'error_id' => uniqid()
            ]);
        } catch (\Exception $e) {}
        
        $this->logger->error('[GLOBAL_HANDLER] Unhandled exception: ' . $exception::class . ' - ' . $exception->getMessage(), [
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString()
        ]);
    }

    private function getUserMessage(\Throwable $exception): string {
        return match ($exception::class) {
            \src\Exceptions\WriteToDatabaseException::class => 'Failed to save to database. Please try again.',
            \src\Exceptions\FoodAlreadyRegistered::class => 'The food is already registered.',
            \Doctrine\DBAL\Exception\UniqueConstraintViolationException::class => 'This record already exists.',
            default => 'An unexpected error occurred. Please try again later.'
        };
    }
}

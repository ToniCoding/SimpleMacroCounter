<?php

namespace src\EventListeners;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\{RequestStack, RedirectResponse};
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[AsEventListener(event: 'kernel.exception', priority: 255)]
class GlobalExceptionListener {
    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
        private RequestStack $requestStack,
    ) {}

    public function onKernelException(ExceptionEvent $event): void {
        $request = $event->getRequest();
        
        if ($request->getPathInfo() === '/error') {
            return;
        }
        $exception = $event->getThrowable();
        
        $session = $this->requestStack->getSession();
        $session->set('last_exception', [
            'message' => $exception->getMessage(),
            'code' => $exception->getCode(),
            'class' => $exception::class,
            'status_code' => $exception instanceof HttpExceptionInterface 
                ? $exception->getStatusCode() 
                : 500,
            'user_message' => $this->getUserMessage($exception)
        ]);
        
        $url = $this->urlGenerator->generate('app_error');
        $event->setResponse(new RedirectResponse($url));
    }

    private function getUserMessage(\Throwable $exception): string {
        return match ($exception::class) {
            \src\Exceptions\WriteToDatabaseException::class => 'Failed to save to database.',
            \Doctrine\DBAL\Exception\UniqueConstraintViolationException::class => 'The registry already exists.',
            \Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class => 'Page not found.',
            \Symfony\Component\Security\Core\Exception\AccessDeniedException::class => 'Not allowed. Try logging in.',

            default => 'Ha ocurrido un error inesperado'
        };
    }
}

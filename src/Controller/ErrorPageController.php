<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Component\Routing\Annotation\Route;

class ErrorPageController extends AbstractController {
    #[Route('/error', name: 'app_error', methods: ['GET'])]
    public function error(Request $request): Response {
        $session = $request->getSession();
        $lastException = $session->get('last_exception');

        $session->remove('last_exception');

        if (!$lastException) {
            return $this->redirectToRoute('home');
        }

        $userMessage = $lastException['user_message'] ?? 'Ha ocurrido un error inesperado';
        
        return $this->render('ErrorTemplate.twig.html', [
            'errorMessage' => $userMessage,
            'errorCode' => $lastException['status_code'] ?? $lastException['code'] ?? 500,
            'debugMessage' => $this->getParameter('kernel.environment') === 'dev' 
                ? ($lastException['message'] ?? null)
                : null
        ]);
    }
}
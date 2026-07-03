<?php

namespace App\Controller;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\{HttpFoundation\JsonResponse, Routing\Annotation\Route};
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

class JWTController extends AbstractController {
    #[Route('/api/generate-jwt', name: 'api_generate_jwt', methods: ['POST'])]
    public function generateJWT(Security $security, JWTTokenManagerInterface $jwtManager): JsonResponse {
        $user = $security->getUser();

        if (!$user) {
            return $this->json(['error' => 'Anonymous user detected.'], 401);
        }

        $token = $jwtManager->create($user);

        return $this->json(['token' => $token]);
    }
}

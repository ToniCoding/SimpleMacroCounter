<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Service\UserMacrosRetrieve;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\{Routing\Annotation\Route, Serializer\SerializerInterface, HttpFoundation\JsonResponse};

class HomePageApiController extends AbstractController {
    public function __construct (
        private UserMacrosRetrieve $userMacrosRetrieve,
        private SerializerInterface $serializer
    ){}

    #[Route(['/api/today-progress'], name: 'todayProgress', methods: 'GET')]
    public function getTodayProgress(User $user): JsonResponse {
        $userProgress = $this->userMacrosRetrieve->calculateUserProgress($user);
        $userGoals = $this->userMacrosRetrieve->getUserWeeklyGoal($user);
    }
}

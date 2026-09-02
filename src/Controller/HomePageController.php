<?php

namespace App\Controller;

use App\DTO\UserMacros\Response\TodayProgressResponseDTO;
use App\Entity\User;
use App\Service\{DailyIntakeRecordService, MacrosRetrieveService};

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Response};
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

class HomePageController extends AbstractController {
    public function __construct(
        private MacrosRetrieveService $macrosRetrieveService,
        private DailyIntakeRecordService $dailyIntakeRecordService
    ) {}

    #[Route(['/', '/home'], name: 'home', methods: ['GET'])]
    public function home(JWTTokenManagerInterface $jwtManager): Response {
        $user = $this->getUser();

        $jwtToken = $jwtManager->create($user);

        return $this->render('HomePageTemplate.twig.html', [
            'jwtToken' => $jwtToken,
        ]);
    }

    // #[Route(['/', '/home'], name: 'home', methods: 'GET')]
    // public function home(): Response {
    //     return $this->render('HomePageTemplate.twig.html');
    // }
    
    /**
     * Provides information about the user progress for the day in progress.
     * @param User $user - Current authenticated user.
     * @return JsonResponse - Request user information.
     */
    #[Route(['/api/today-progress'], name: 'todayProgress', methods: 'GET')]
    public function getTodayProgress(#[CurrentUser] User $user): JsonResponse {
        $todayUserMacroGramsConsumed = $this->dailyIntakeRecordService->ensureDailyIntakeRecord($user);
        $userWeeklyCalorieGoal = $this->macrosRetrieveService->getWeeklyCalorieGoal($user);
        $userWeeklyConsumedCalories = $this->macrosRetrieveService->getCaloriesConsumedForThisWeek($user);

        $nutritionDto = new TodayProgressResponseDTO(
            todayMacrosProgress: $this->macrosRetrieveService->calculateUserProgress($user),
            todayUserMacroGrams: $todayUserMacroGramsConsumed->__toArray(),
            dailyMacroGramsGoal: $this->dailyIntakeRecordService->ensureOneMacroGoal($user)->__toArray(), 
            weeklyCalorieGoal: $userWeeklyCalorieGoal,
            weeklyCalorieConsumption: $userWeeklyConsumedCalories,
            weeklyCalorieGoalRiskInfo: $this->macrosRetrieveService->calculateWeeklyRisk($userWeeklyCalorieGoal, $userWeeklyConsumedCalories, $todayUserMacroGramsConsumed->getCalories())
        );

        return $this->json($nutritionDto);
    }
}

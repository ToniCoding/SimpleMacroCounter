<?php

namespace App\Controller;

use App\DTO\TodayProgressResponseDTO;
use App\Entity\User;
use App\Service\UserMacrosRetrieve;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Response, JsonResponse};
use Symfony\Component\Routing\Annotation\Route;

class HomePageController extends AbstractController {
    public function __construct(
        private UserMacrosRetrieve $userMacrosRetrieve,
    ) {}

    #[Route(['/', '/home'], name: 'home', methods: 'GET')]
    public function home(): Response {
        $user = $this->getUser();

        $userProgress = $this->userMacrosRetrieve->calculateUserProgress($user);
        $userGoals = $this->userMacrosRetrieve->getMacroGoalsArr($user);
        $macrosConsumed = $this->userMacrosRetrieve->getConsumedMacrosArr($user);
        $weeklyGoal = $this->userMacrosRetrieve->getUserWeeklyCalorieGoal($user);
        $weeklyConsumption = $this->userMacrosRetrieve->getCaloriesConsumedForCurrentWeek($user);
        
        $weeklyGoalDanger = $this->userMacrosRetrieve->calculateWeeklyRisk($weeklyGoal, $weeklyConsumption, $macrosConsumed['calories']);
        $weeklyRisk = str_replace('_', ' ', ucfirst($weeklyGoalDanger['risk']));
        $riskColor = $weeklyGoalDanger['risk_color'];

        return $this->render('HomePageTemplate.twig.html', [
            'message' => 'Welcome to SMC',
            'caloricProgress' => $userGoals['caloriesGoal'] - $macrosConsumed['calories'],
            'caloriesConsumed' => $macrosConsumed['calories'],
            'calorieGoal' => $userGoals['caloriesGoal'],
            'weeklyGoal' => $weeklyGoal,
            'weeklyConsumption' => $weeklyConsumption,
            'weeklyRisk' => $weeklyRisk,
            'riskColor' => $riskColor,
            ...$userProgress,
            ...$userGoals,
            ...$macrosConsumed
        ]);
    }

    #[Route(['/api/today-progress'], name: 'todayProgress', methods: 'GET')]
    public function getTodayProgress(User $user): JsonResponse {
        try {
            $todayUserPercentageProgress = $this->userMacrosRetrieve->calculateUserProgress($user);
            $todayUserMacroGramsConsumed = $this->userMacrosRetrieve->getConsumedMacros($user);
            $userWeeklyCalorieGoal = $this->userMacrosRetrieve->getUserWeeklyCalorieGoal($user);
            $userWeeklyConsumedCalories = $this->userMacrosRetrieve->getCaloriesConsumedForCurrentWeek($user);
        } catch (\Throwable $e) {
            $errorMessage = ['message' => 'Data not found for the current user.'];
            return $this->json($errorMessage, 404);
        }

        $nutritionDto = new TodayProgressResponseDTO(
            todayMacrosProgress: $todayUserPercentageProgress,
            todayUserMacroGrams: $this->userMacrosRetrieve->getConsumedMacrosArr($user),
            weeklyCalorieGoal: $userWeeklyCalorieGoal,
            weeklyCalorieConsumption: $userWeeklyConsumedCalories,
            weeklyCalorieGoalRiskInfo: $this->userMacrosRetrieve->calculateWeeklyRisk($userWeeklyCalorieGoal, $userWeeklyConsumedCalories, $todayUserMacroGramsConsumed->getCalories())
        );

        return $this->json($nutritionDto);
    }
}

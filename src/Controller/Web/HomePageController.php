<?php

namespace App\Controller\Web;

use App\Service\UserMacrosRetrieve;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
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
        $weeklyGoal = $this->userMacrosRetrieve->getUserWeeklyGoal($user);
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
}

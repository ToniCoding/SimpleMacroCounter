<?php

namespace src\Controller;

use src\Service\UserMacrosRetrieve;

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

        return $this->render('HomePageTemplate.twig', [
            'message' => 'Welcome to SMC',
            'caloricProgress' => $userGoals['caloriesGoal'] - $macrosConsumed['calories'],
            'caloriesConsumed' => $macrosConsumed['calories'],
            'calorieGoal' => $userGoals['caloriesGoal'],
            ...$userProgress,
            ...$userGoals,
            ...$macrosConsumed
        ]);
    }
}

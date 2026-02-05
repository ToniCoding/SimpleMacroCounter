<?php

namespace src\Controller;

use src\Entity\User;
use src\Exceptions\NoRecordFoundException;
use src\Service\UserMacrosRetrieve;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomePageController extends AbstractController {
    public function __construct(private UserMacrosRetrieve $userMacrosRetrieve) {}

    #[Route(['/', '/home'], name: 'home')]
    public function home(): Response {
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw $this->createAccessDeniedException('User not found');
        }
        
        try {
            $macrosProgress = $this->userMacrosRetrieve->calculateUserProgress($user);
            $macrosConsumed = $this->userMacrosRetrieve->getConsumedMacros($user);
            $macroGoals = $this->userMacrosRetrieve->getMacroGoals($user);

            $userProgress = [
                'proteinProgress' => $macrosProgress->getProtein(),
                'carbProgress' => $macrosProgress->getCarbs(),
                'fatProgress' => $macrosProgress->getFats(),
                'fiberProgress' => $macrosProgress->getFiber(),
                'calorieProgress' => $macrosProgress->getCalories()
            ];

            $userGoals = [
                'proteinGoal' => $macroGoals->getProtein(),
                'carbGoal' => $macroGoals->getCarbs(),
                'fatGoal' => $macroGoals->getFats(),
                'fiberGoal' => $macroGoals->getFiber(),
                'caloriesGoal' => $macroGoals->getCalories()
            ];

            $grams = [
                'proteinGrams' => $macrosConsumed->getProtein(),
                'carbGrams' => $macrosConsumed->getCarbs(),
                'fatGrams' => $macrosConsumed->getFats(),
                'fiberGrams' => $macrosConsumed->getFiber(),
                'calories' => $macrosConsumed->getCalories()
            ];

            return $this->render('HomePageTemplate.twig', [
                'message' => 'Welcome to SMC',
                'user' => $user->getUsername(),
                'caloriesConsumed' => $macroGoals->getCalories() - $macrosConsumed->getCalories(),
                ...$userProgress,
                ...$userGoals,
                ...$grams
            ]);
        } catch (NoRecordFoundException) {
            return $this->render('HomePageTemplate.twig', [
                'error' => 'No record found for you in our database :('
            ]);
        }
    }
}

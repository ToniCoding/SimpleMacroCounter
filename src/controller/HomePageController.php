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

            return $this->render('HomePageTemplate.twig', [
                'message' => 'Bienvenido a SMC',
                'user' => $user->getUsername(),
                'proteinProgress' => $macrosProgress->getProtein(),
                'carbProgress' => $macrosProgress->getCarbs(),
                'fatProgress' => $macrosProgress->getFats()
            ]);
        } catch (NoRecordFoundException) {
            return $this->render('HomePageTemplate.twig', [
                'error' => 'No record found for you in our database :('
            ]);
        }
    }
}

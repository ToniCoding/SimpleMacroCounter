<?php

namespace App\Controller;

use App\Entity\User;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomePageController extends AbstractController {
    public function __construct() {}

    #[Route('/', name: 'home')]
    public function home(): Response {
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw $this->createAccessDeniedException('User not found');
        }

        return $this->render('HomePageTemplate.twig', [
            'message' => 'Bienvenido a SMC',
            'user' => $user->getUsername()
        ]);
    }
}

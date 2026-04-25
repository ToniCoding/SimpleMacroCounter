<?php


namespace src\Controller;

use src\Entity\User;
use src\Service\FoodRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AddFoodsPageController extends AbstractController {

    #[Route('/addfood', name: 'addfood')]
    public function addfood(Request $request, FoodRegistry $foodRegistry) {
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw $this->createAccessDeniedException('User not found');
        }

        if ($request->getMethod() == 'POST') {
            return $foodRegistry->registerFoodIntake(json_decode($request->getContent(), true), $user);
        }

        return $this->render('AddFoodTemplate.twig');
    }
}

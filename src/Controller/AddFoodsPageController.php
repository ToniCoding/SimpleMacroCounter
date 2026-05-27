<?php

namespace src\Controller;

use src\Entity\User;
use src\Service\FoodRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request, Response, JsonResponse};
use Symfony\Component\Routing\Annotation\Route;

class AddFoodsPageController extends AbstractController {
    #[Route('/addfood', name: 'addFoodCatalog', methods: 'GET')]
    public function addfood(Request $request, FoodRegistry $foodRegistry): Response {
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw $this->createAccessDeniedException('User not found');
        }

        $foodCatalogPagination = (int) $request->query->get('pagination', 1);
        $marketFilter = (string) $request->query->get('market', '');

        return $this->render('AddFoodTemplate.twig', [
            'foodCatalog' => $foodRegistry->getFoodsByMarket(
                $foodCatalogPagination,
                $marketFilter,
                ''
            )
        ]);
    }

    #[Route('/addfood', name: 'addFoodProcessing', methods: 'POST')]
    public function addFoodPost(Request $request, FoodRegistry $foodRegistry): JsonResponse {
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw $this->createAccessDeniedException('User not found');
        }

        $data = json_decode($request->getContent(), true);

        if ($data === null) {
            return new JsonResponse([
                'error' => 'Invalid JSON'
            ], 400);
        }

        $result = $foodRegistry->registerFoodIntake($data, $user);

        if ($result) {
            $this->addFlash('registerFoodStatus', 'Successfully registered the intake!');

            return new JsonResponse([
                'success' => true,
                'redirect' => '/home'
            ], 200);
        }

        return new JsonResponse([
            'success' => false,
            'redirect' => '/home'
        ], 500); # This should be sending something in accordance to the error. TechDebt.
    }
}

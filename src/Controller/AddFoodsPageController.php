<?php

namespace src\Controller;

use src\Entity\User;
use src\Service\FoodRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request, Response, RedirectResponse, JsonResponse};
use Symfony\Component\Routing\Annotation\Route;

class AddFoodsPageController extends AbstractController {
    #[Route('/addfood', name: 'addfood', methods: ['GET', 'POST'])]
    public function addfood(Request $request, FoodRegistry $foodRegistry,): Response|RedirectResponse {
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw $this->createAccessDeniedException('User not found');
        }

        $foodCatalogPagination = (int) $request->query->get('pagination', 1);
        $marketFilter = (string) $request->query->get('market', '');

        if ($request->isMethod('POST')) {
            $data = json_decode($request->getContent(), true);

            if ($data === null) {
                return new JsonResponse(['error' => 'Invalid JSON'], 400);
            }

            $result = $foodRegistry->registerFoodIntake($data, $user);

            if ($result) {
                $this->addFlash('registerFoodStatus', 'Successfully registered the intake!');

                return new JsonResponse([
                    'success' => true,
                    'redirect' => '/home'
                ]);
            }
        }

        return $this->render('AddFoodTemplate.twig', [
            'foodCatalog' => $foodRegistry->getFoodsByMarket(
                $foodCatalogPagination,
                $marketFilter,
                ''
            )
        ]);
    }
}

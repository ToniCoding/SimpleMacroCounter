<?php

namespace src\Controller;

use src\Service\FoodRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request, Response, JsonResponse};
use Symfony\Component\Routing\Annotation\Route;

class AddFoodsPageController extends AbstractController {
    #[Route('/addfood', name: 'addFoodCatalog', methods: 'GET')]
    public function addfood(Request $request, FoodRegistry $foodRegistry): Response {
        $foodCatalogPagination = (int) $request->query->get('pagination', 1);
        $marketFilter = (string) $request->query->get('market', '');
    
        $catalog = $foodRegistry->getProductsByMarket(
            $foodCatalogPagination,
            $marketFilter,
            'human'
        );

        $catalogJson = json_encode($catalog, JSON_UNESCAPED_UNICODE);

        return $this->render('AddFoodTemplate.twig', [
            'foodCatalog' => $catalogJson
        ]);
    }

    #[Route('/addfood', name: 'addFoodProcessing', methods: 'POST')]
    public function addFoodPost(Request $request, FoodRegistry $foodRegistry): JsonResponse {
        $this->isGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

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
            'redirect' => '/addfood'
        ], 500); # This should be sending something in accordance to the error. TechDebt.
    }

    #[Route('/api/search-products', name: 'api_search_products', methods: 'GET')]
    public function searchProducts(Request $request, FoodRegistry $foodRegistry): JsonResponse {
        $query = $request->query->get('q', '');

        if (\strlen(trim($query)) < 2) {
            return $this->json([]);
        }

        $results = $foodRegistry->searchProductsByFullText($query, 125);

        return $this->json($results);
    }
}

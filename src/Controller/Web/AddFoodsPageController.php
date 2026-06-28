<?php

namespace App\Controller\Web;

use App\Service\FoodRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\{Request, Response, JsonResponse};
use Symfony\Component\Routing\Annotation\Route;

class AddFoodsPageController extends AbstractController {
    public function __construct(
        private ParameterBagInterface $params
    ) {}
    #[Route('/addfood', name: 'addFoodCatalog', methods: 'GET')]
    public function addfood(Request $request, FoodRegistry $foodRegistry): Response {
        $page = (int) $request->query->get('pagination', 1);
        $marketFilter = (string) $request->query->get('market', '');
        $limit = $this->params->get('food_pagination:max_products_per_page');

        $catalogData = $foodRegistry->getProductsByMarket($page, $marketFilter, 'human', $limit);

        $catalogJson = json_encode($catalogData['data'], JSON_UNESCAPED_UNICODE);

        return $this->render('AddFoodTemplate.twig.html', [
            'foodCatalog' => $catalogJson,
            'foodCatalogPagination' => json_encode($catalogData['pagination'], JSON_UNESCAPED_UNICODE),
            'pagination' => $catalogData['pagination'],
            'currentPage' => $catalogData['pagination']['currentPage'],
            'totalPages' => $catalogData['pagination']['totalPages'],
            'marketFilter' => $marketFilter
        ]);
    }

    #[Route('/addfood', name: 'addFoodProcessing', methods: 'POST')]
    public function addFoodPost(Request $request, FoodRegistry $foodRegistry): JsonResponse {
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
        $page = (int) $request->query->get('page', 1);
        $limit = $this->params->get('food_pagination:max_products_per_page');

        if (\strlen(trim($query)) < 2) {
            return $this->json([
                'data' => [],
                'pagination' => [
                    'currentPage' => 1,
                    'totalPages' => 0,
                    'totalItems' => 0,
                    'itemsPerPage' => $limit,
                    'hasNext' => false,
                    'hasPrevious' => false
                ]
            ]);
        }

        $results = $foodRegistry->searchProductsByFullText($query, $page, $limit);

        return $this->json($results);
    }
}

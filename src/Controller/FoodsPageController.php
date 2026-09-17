<?php

namespace App\Controller;

use App\DTO\ProductsDTO;
use App\Service\FoodRegistry;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, RedirectResponse, Request, Response};
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller responsible for rendering the food management page and providing 
 * a REST API endpoint for registering new food items.
 */
class FoodsPageController extends AbstractController {
    
    /**
     * Initializes the controller with the required food registry service.
     * 
     * @param FoodRegistry $foodRegistry Service to handle food creation and persistence.
     */
    public function __construct(
        private FoodRegistry $foodRegistry
    ) {}

    /**
     * Renders the food management view for registering new food products.
     * 
     * @param Request $request The incoming HTTP request.
     * @return Response|RedirectResponse Returns the rendered template.
     */
    #[Route(['/foods'], name: 'foods', methods: ['GET'])]
    public function foods(Request $request): Response | RedirectResponse {
        return $this->render('FoodManagementTemplate.twig.html', [
            'page_title' => 'Register new food - SMC'
        ]);
    }

    /**
     * REST API endpoint for registering a new food item via validated request payloads.
     * 
     * @param ProductsDTO $productDto The validated DTO containing new food product details.
     * @return JsonResponse Returns a JSON response with a success message and redirect URL.
     */
    #[Route(['/api/v1/register-food'], name: 'register-food', methods: ['POST'])]
    public function registerNewFood(
        #[MapRequestPayload] ProductsDTO $productDto
    ): JsonResponse {
        $user = $this->getUser();

        $this->foodRegistry->createFood($productDto, $user);

        return $this->json([
            'message' => 'Sucessfully registered the new food!',
            'redirect_url' => $this->generateUrl('home')
        ], 200);
    }
}

<?php

namespace App\Controller;

use App\DTO\ProductsDTO;
use App\Form\RegisterFoodsType;
use App\Service\FoodRegistry;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, RedirectResponse, Request, Response};
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;

class FoodsPageController extends AbstractController {
    public function __construct(
        private FoodRegistry $foodRegistry
    ) {}

    #[Route(['/foods'], name: 'foods', methods: ['GET'])]
    public function foods(Request $request): Response | RedirectResponse {
        return $this->render('FoodManagementTemplate.twig.html', [
            'page_title' => 'Register new food - SMC'
        ]);
    }

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

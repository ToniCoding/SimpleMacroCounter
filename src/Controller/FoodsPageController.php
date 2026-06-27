<?php

namespace src\Controller;

use src\DTO\ProductsDTO;
use src\Form\RegisterFoodsType;
use src\Service\FoodRegistry;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{RedirectResponse, Request, Response};
use Symfony\Component\Routing\Annotation\Route;

class FoodsPageController extends AbstractController {
    public function __construct(
        private FoodRegistry $foodRegistry
    ) {}

    #[Route(['/foods'], name: 'foods', methods: ['GET', 'POST'])]
    public function foods(Request $request): Response | RedirectResponse {
        $user = $this->getUser();

        $form = $this->createForm(RegisterFoodsType::class, new ProductsDTO());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->foodRegistry->createFood($form->getData(), $user);
            $this->addFlash('registerFoodStatus', 'Successfully registered the food!');

            return $this->redirectToRoute('home');
        }

        return $this->render('FoodManagementTemplate.twig.html', [
            'form' => $form,
            'page_title' => 'Register new food - SMC'
        ]);
    }
}

<?php

namespace src\Controller;

use src\DTO\FoodDTO;
use src\Form\RegisterFoodsType;
use src\Service\FoodRegistry;
use src\Entity\User;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{RedirectResponse, Request, Response};
use Symfony\Component\Routing\Annotation\Route;

class FoodsPageController extends AbstractController
{
    public function __construct(
        private FoodRegistry $foodRegistry
    ) {}

    #[Route(['/foods'], name: 'foods')]
    public function foods(Request $request): Response|RedirectResponse
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw $this->createAccessDeniedException('User not found');
        }

        $form = $this->createForm(RegisterFoodsType::class, new FoodDTO());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->foodRegistry->createFood($form->getData(), $user);
            $this->addFlash('registerFoodStatus', 'Successfully registered the food!');

            return $this->redirectToRoute('home');
        }

        return $this->render('FoodManagementTemplate.twig', [
            'form' => $form,
            'user' => $user->getUsername(),
            'page_title' => 'Register new food - SMC'
        ]);
    }
}

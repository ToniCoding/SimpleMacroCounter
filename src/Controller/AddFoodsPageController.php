<?php

namespace src\Controller;

use src\Service\FoodRegistry;
use src\Entity\User;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{RedirectResponse, Request, Response};
use Symfony\Component\Routing\Annotation\Route;

class AddFoodsPageController extends AbstractController {
    public function __construct(
        private FoodRegistry $foodRegistry
    ) {}

    #[Route(['/addfood'], name: 'addfood')]
    public function addfood(Request $request): Response | RedirectResponse {
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw $this->createAccessDeniedException('User not found');
        }

        $selectedMarket = $request->query->get('market', '');
        $foodsPage = $request->query->get('offset', 1);

        $foodsResult = $this->foodRegistry->getFoodsByMarket($selectedMarket, $foodsPage);

        return $this->render('dummy/DummyVariablesTemplate.twig', [
            'testdata' => $foodsResult
        ]);
    }
}

<?php

namespace src\Controller;

use Psr\Log\LoggerInterface;
use src\Entity\User;
use src\Service\FoodRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class AddFoodsPageController extends AbstractController
{
    #[Route('/addfood', name: 'addfood', methods: ['GET', 'POST'])]
    public function addfood(
        Request $request,
        FoodRegistry $foodRegistry,
        LoggerInterface $logger
    ): Response|RedirectResponse {
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw $this->createAccessDeniedException('User not found');
        }

        $foodCatalogPagination = (int) $request->query->get('pagination', 1);
        $marketFilter = (string) $request->query->get('market', '');

        if ($request->isMethod('POST')) {
            $data = json_decode($request->getContent(), true);

            $logger->info('Food intake request received', [
                'raw_body' => $request->getContent(),
                'decoded_data' => $data,
                'user_id' => $user->getId() ?? null
            ]);

            if ($data === null) {
                $logger->error('Invalid JSON received for food intake', [
                    'raw_body' => $request->getContent()
                ]);

                return new JsonResponse(['error' => 'Invalid JSON'], 400);
            }

            $result = $foodRegistry->registerFoodIntake($data, $user);

            $logger->info('Food intake processing result', [
                'result' => $result,
                'food_id' => $data['id'] ?? null,
                'grams' => $data['grams'] ?? null
            ]);

            if ($result) {
                $this->addFlash('registerFoodStatus', 'Successfully registered the intake!');

                $logger->info('Food intake successfully registered', [
                    'user_id' => $user->getId() ?? null
                ]);

                return new JsonResponse([
                    'success' => true,
                    'redirect' => '/home'
                ]);
            }

            $logger->warning('Food intake failed', [
                'data' => $data
            ]);
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

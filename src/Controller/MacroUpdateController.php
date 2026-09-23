<?php

namespace App\Controller;

use App\DTO\MacroDataDTO;
use App\Exceptions\ExceededMacroLimitException;
use App\Service\MacroIntakeUpdater;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Response};
use Symfony\Component\{HttpKernel\Attribute\MapRequestPayload, Routing\Annotation\Route, Serializer\SerializerInterface};

/**
 * Controller responsible for managing macronutrient updates through REST API endpoints.
 */

class MacroUpdateController extends AbstractController {
    
    /**
     * Initializes the controller with the required macro intake updater service.
     * 
     * @param MacroIntakeUpdater $macroIntakeUpdater Service to handle business logic for updating macros.
     */
    public function __construct(
        private MacroIntakeUpdater $macroIntakeUpdater,
    ) {}

    /**
     * Handles web form rendering and submission for modifying user macronutrients.
     * 
     * @return Response Returns the rendered template.
     */
    #[Route(['/modifyMacros', '/modifymacros'], name: 'modifyMacros', methods: 'GET')]
    public function modifyMacros(): Response {
        return $this->render('modifyData/ModifyMacrosTemplate.twig.html', [
            'page_title' => 'Modify macros - SMC',
        ]);
    }

    /**
     * Shared internal helper to process macro updates, manage exceptions, and dispatch flash messages.
     * 
     * @param MacroDataDTO $macroDto The DTO containing updated macronutrient data.
     * @param bool $apiRs Flag indicating whether the request originated from the API (true) or web form (false).
     * @return Response|bool Returns a redirect response for web requests, or a boolean success flag for API calls.
     */
    private function handleMacrosModification(MacroDataDTO $macroDto, bool $apiRs): Response | bool {
        $user = $this->getUser();

        try {
            $this->macroIntakeUpdater->updateMacroIntake($user, $macroDto, $macroDto->getIntent());
            $this->addFlash('modifyMacrosStatusSuccess', 'Successfully modified macros.');
        } catch (ExceededMacroLimitException $e) {
            $this->addFlash('modifyMacrosStatusError', $e->getMessage());
        }
        
        if (!$apiRs) return $this->redirectToRoute('home');

        return true;
    }

    /**
     * REST API endpoint for updating macronutrients via JSON payloads.
     * 
     * @return JsonResponse Returns a JSON response with status codes (200, 400, or 500).
     */
    #[Route(['/api/v1/modify-macros'], name: "apiModifyMacros", methods: 'POST')]
    public function updateWithNewMacros(
        #[MapRequestPayload] MacroDataDTO $macroDataDto
    ): JsonResponse {
        if ($this->handleMacrosModification($macroDataDto, true)) {
            return $this->json([
                'message' => 'Sucessfully updated the macro-nutrient intake!',
                'redirect_url' => $this->generateUrl('home')
            ], 200);
        }

        return $this->json(['errorMessage' => 'There was an error processing the request for updating the macro-nutrient update.'], 500);
    }
}

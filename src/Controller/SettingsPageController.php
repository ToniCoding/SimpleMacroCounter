<?php

namespace App\Controller;

use App\DTO\MacroSettingsDTO;
use App\Entity\User;
use App\Service\DailyIntakeRecordService;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response, RedirectResponse};
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

/**
 * Controller responsible for managing user settings, including macro-nutrient goal 
 * configurations through both traditional web forms and REST API endpoints.
 */
class SettingsPageController extends AbstractController {
    
    /**
     * Initializes the controller with the required daily intake record service.
     * 
     * @param DailyIntakeRecordService $dailyIntakeRecord Service to handle daily intake records and macro goals.
     */
    public function __construct(
        private DailyIntakeRecordService $dailyIntakeRecord,
    ) {}

    /**
     * Renders the settings view and handles web form submissions for updating macro-nutrient goals.
     * 
     * @param Request $request The incoming HTTP request.
     * @return Response|RedirectResponse Returns the rendered settings template or redirects on success.
     */
    #[Route('/settings', name: 'settings', methods: 'GET')]
    public function settings(Request $request): Response | RedirectResponse {
        return $this->render('SettingsTemplate.twig.html', [
            'page_title' => 'Settings - SMC',
        ]);
    }

    /**
     * REST API endpoint for updating user macronutrient goals via JSON payloads.
     * 
     * @param User $user The user entity targeted for updates.
     * @return JsonResponse Returns a JSON response with status codes (200, 400, or 500).
     */
    #[Route('/api/v1/settings', name: 'apiSettings', methods: 'PUT')]
    public function applySettings(
        #[MapRequestPayload] MacroSettingsDTO $macroSettingsDto,
        #[CurrentUser] User $user
    ): JsonResponse {
        if ($this->dailyIntakeRecord->modifyMacroGoal($user, $macroSettingsDto)) {
            return $this->json(['successMessage' => 'Successfully updated the macro-nutrient goal.'], 200);
        }

        return $this->json(['errorMessage' => 'There was an error processing the request for updating the macro-nutrient goal.'], 500);
    }
}

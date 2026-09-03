<?php

namespace App\Controller;

use App\DTO\MacroSettingsDTO;
use App\Entity\User;
use App\Form\MacroGoalsSettingsType;
use App\Service\DailyIntakeRecordService;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response, RedirectResponse};
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SettingsPageController extends AbstractController {
    public function __construct(
        private DailyIntakeRecordService $dailyIntakeRecord,
    ) {}

    #[Route('/settings', name: 'settings', methods: ['GET', 'POST'])]
    public function settings(Request $request): Response | RedirectResponse {
        $user = $this->getUser();
        
        $macroSettingsForm = $this->createForm(MacroGoalsSettingsType::class, new MacroSettingsDTO());
        $macroSettingsForm->handleRequest($request);

        if ($macroSettingsForm->isSubmitted() && $macroSettingsForm->isValid()) { // DATA OK
            if ($this->dailyIntakeRecord->modifyMacroGoal($user, $macroSettingsForm->getData())) {
                return $this->redirect('home');
            };
        }

        return $this->render('SettingsTemplate.twig.html', [
            'form' => $macroSettingsForm,
            'page_title' => 'Settings - SMC',
            'error' => ''
        ]);
    }

    #[Route('/api/v1/settings', name: 'apiSettings', methods: 'POST')]
    public function applySettings(Request $request, SerializerInterface $serializerInterface, ValidatorInterface $validatorInterface, User $user): JsonResponse {
        $requestBody = $request->getContent();
        
        try {
            $mappedDto = $serializerInterface->deserialize($requestBody, MacroSettingsDTO::class, 'json');
        } catch (\Exception $ex) {
            return $this->json(['errorMessage' => $ex->getMessage()], 400);
        }

        $dtoErrors = $validatorInterface->validate($mappedDto);
        if (\count($dtoErrors) > 0) {
            return $this->json(['errorMessage' => (string) $dtoErrors], 400);
        }

        if ($this->dailyIntakeRecord->modifyMacroGoal($user, $mappedDto)) {
            return $this->json(['successMessage' => 'Successfully updated the macro-nutrient goal.'], 200);
        }
        
        return $this->json(['errorMessage' => 'There was an error processing the request for updating the macro-nutrient goal.'], 500);
    }
}

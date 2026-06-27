<?php

namespace src\Controller;

use src\DTO\MacroSettingsDTO;
use src\Form\MacroGoalsSettingsType;
use src\Service\DailyIntakeRecord;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request, Response, RedirectResponse};
use Symfony\Component\Routing\Annotation\Route;

class SettingsPageController extends AbstractController {
    public function __construct(
        private DailyIntakeRecord $dailyIntakeRecord,
    ) {}

    #[Route('/settings', name: 'settings', methods: ['GET', 'POST'])]
    public function settings(Request $request): Response | RedirectResponse {
        $user = $this->getUser();
        
        $macroSettingsForm = $this->createForm(MacroGoalsSettingsType::class, new MacroSettingsDTO());
        $macroSettingsForm->handleRequest($request);

        if ($macroSettingsForm->isSubmitted() && $macroSettingsForm->isValid()) {
            $this->dailyIntakeRecord->modifyMacroGoal($user, $macroSettingsForm->getData());
        }

        return $this->render('SettingsTemplate.twig.html', [
            'form' => $macroSettingsForm,
            'page_title' => 'Settings - SMC',
            'error' => ''
        ]);
    }
}

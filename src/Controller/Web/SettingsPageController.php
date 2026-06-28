<?php

namespace App\Controller\Web;

use App\DTO\MacroSettingsDTO;
use App\Form\MacroGoalsSettingsType;
use App\Service\DailyIntakeRecord;

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
}

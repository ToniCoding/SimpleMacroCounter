<?php

namespace src\Controller;

use src\DTO\MacroDataDTO;
use src\Exceptions\ExceededMacroLimitException;
use src\Form\ModifyMacrosType;
use src\Service\MacroIntakeUpdater;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Component\Routing\Annotation\Route;

class MacroUpdateController extends AbstractController {
    public function __construct(
        private MacroIntakeUpdater $macroIntakeUpdater,
    ) {}

    #[Route(['/modifyMacros', '/modifymacros'], name: 'modifyMacros', methods: ['GET', 'POST'])]
    public function modifyMacros(Request $request): Response {
        $action = $request->query->get('action');

        return match ($action) {
            'increase' => $this->addMacros($request),
            'reduce' => $this->reduceMacros($request),
            default => $this->redirectToRoute('home')
        };
    }

    private function handleMacrosModification(Request $request, string $intent, string $pageTitle): Response {
        $user = $this->getUser();

        $form = $this->createForm(ModifyMacrosType::class, new MacroDataDTO(0, 0, 0, 0, 0));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $method = $intent === 'increase' ? 'addMacros' : 'reduceMacros';
                $extraParam = $intent === 'reduce' ? ['reduce'] : [];
                $this->macroIntakeUpdater->$method($user, $form->getData(), ...$extraParam);

                return $this->redirectToRoute('home');
            } catch (ExceededMacroLimitException $ex) {
                $this->addFlash('modifyMacrosStatus', $ex->getMessage());
            }
        }

        return $this->render('modifyData/ModifyMacrosTemplate.twig', [
            'form' => $form,
            'page_title' => $pageTitle,
            'page_intent' => $intent === 'increase' ? 'Add macro-nutrient count' : 'Reduce macro-nutrient count',
        ]);
    }

    private function addMacros(Request $request): Response {
        return $this->handleMacrosModification($request, 'increase', 'Add macros - SMC');
    }

    private function reduceMacros(Request $request): Response {
        return $this->handleMacrosModification($request, 'reduce', 'Reduce macros - SMC');
    }
}

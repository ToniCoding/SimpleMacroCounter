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
        $macroDto = new MacroDataDTO();
        $form = $this->createForm(ModifyMacrosType::class, $macroDto);

        $form->handleRequest($request);

        if ($request->isMethod('POST') && $form->isSubmitted()) {
            return $this->handleMacrosModification($macroDto);
        }

        return $this->render('modifyData/ModifyMacrosTemplate.twig', [
            'form' => $form,
            'page_title' => 'Modify macros - SMC',
        ]);
    }

    private function handleMacrosModification(MacroDataDTO $macroDto): Response {
        $user = $this->getUser();

        try {
            $this->macroIntakeUpdater->updateMacroIntake($user, $macroDto, $macroDto->getIntent());
            $this->addFlash('modifyMacrosStatusSuccess', 'Successfully modified macros.');
        } catch (ExceededMacroLimitException $e) {
            $this->addFlash('modifyMacrosStatusError', $e->getMessage());
        }

        return $this->redirectToRoute('home');
    }
}
<?php

namespace App\Controller;

use App\DTO\MacroDataDTO;
use App\Exceptions\ExceededMacroLimitException;
use App\Form\ModifyMacrosType;
use App\Service\MacroIntakeUpdater;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Component\{Routing\Annotation\Route, Serializer\SerializerInterface};
use Symfony\Component\Validator\Validator\ValidatorInterface;

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
            return $this->handleMacrosModification($macroDto, false);
        }

        return $this->render('modifyData/ModifyMacrosTemplate.twig.html', [
            'form' => $form,
            'page_title' => 'Modify macros - SMC',
        ]);
    }

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

    // After adapting the web page to consume from this API, the handleMacrosModification will need a refactor to start working with user IDs or JWT.
    #[Route(['/api/modify-macros/{userId}'], name: "apiModifyMacros", methods: 'POST')]
    public function updateWithNewMacros(Request $request, SerializerInterface $serializerInterface, ValidatorInterface $validatorInterface) {
        $requestBody = $request->getContent();
        
        try {
            $mappedDto = $serializerInterface->deserialize($requestBody, MacroDataDTO::class, 'json');
        } catch (\Exception $ex) {
            return $this->json(['errorMessage' => $ex->getMessage()], 400);
        }

        $dtoErrors = $validatorInterface->validate($mappedDto);
        if (\count($dtoErrors) > 0) {
            return $this->json(['errorMessage' => (string) $dtoErrors], 400);
        }

        if ($this->handleMacrosModification($mappedDto, true)) {
            return $this->json(['successMessage' => 'Successfully updated the macro-nutrient intake.'], 200);
        }
    }
}
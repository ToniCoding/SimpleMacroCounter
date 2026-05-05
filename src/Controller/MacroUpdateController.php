<?php

namespace src\Controller;

use src\DTO\MacroDataDTO;
use src\Entity\User;
use src\Exceptions\ExceededMacroLimitException;
use src\Form\ModifyMacrosType;
use src\Service\MacroIntakeUpdater;

use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Component\Routing\Annotation\Route;

class MacroUpdateController extends AbstractController {
    public function __construct(
        private EntityManagerInterface $entityManager,
        private MacroIntakeUpdater $macroIntakeUpdater,
    ) {}

    #[Route(['/modifyMacros', '/modifymacros'], name: 'modifyMacros', methods: ['GET', 'POST'])]
    public function modifyMacros(Request $request): Response {
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw $this->createAccessDeniedException('User not found');
        }

        $form = $this->createForm(ModifyMacrosType::class, new MacroDataDTO(0, 0, 0, 0, 0));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->macroIntakeUpdater->updateMacroIntake($user, $form->getData());

                return $this->redirectToRoute('home');
            } catch (ExceededMacroLimitException $ex) {
                $this->addFlash('modifyMacrosStatus', $ex->getMessage());
            }
        }

        return $this->render('modifyData/ModifyMacrosTemplate.twig', [
            'user' => $user->getUsername(),
            'form' => $form,
            'page_title' => 'Add macros - SMC',
            'page_intent' => 'Add macro-nutrient count',
        ]);
    }

    #[Route(['/reduceMacros', '/reducemacros'], name: 'reduceMacros', methods: ['GET', 'POST'])]
    public function reduceMacros(Request $request): Response {
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw $this->createAccessDeniedException('User not found');
        }

        $form = $this->createForm(ModifyMacrosType::class, new MacroDataDTO(0, 0, 0, 0, 0));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->macroIntakeUpdater->updateMacroIntake($user, $form->getData(), 'reduce');

                return $this->redirectToRoute('home');
            } catch (ExceededMacroLimitException $ex) {
                $this->addFlash('modifyMacrosStatus', $ex->getMessage());
            }
        }

        return $this->render('modifyData/ModifyMacrosTemplate.twig', [
            'user' => $user->getUsername(),
            'form' => $form,
            'page_title' => 'Reduce macros - SMC',
            'page_intent' => 'Reduce macro-nutrient count'
        ]);
    }
}

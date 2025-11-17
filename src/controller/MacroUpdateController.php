<?php

namespace src\Controller;

use src\DTO\MacroDataDTO;
use src\Entity\User;
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

        $macroDTO = new MacroDataDTO(0, 0, 0, 0, 0);

        $form = $this->createForm(ModifyMacrosType::class, $macroDTO);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var MacroDataDTO $data */
            $data = $form->getData();

            $data->setCalories(
                $data->getProtein() * 4 +
                $data->getFats() * 9 +
                $data->getCarbs() * 4 +
                $data->getFiber() * 2
            );

            if ($this->macroIntakeUpdater->updateMacroIntake($user, $data)) {
                $this->addFlash('successMessages', 'Successfully added to the macro-nutrient count!');
                
                return $this->redirectToRoute('home');
            }
        }

        return $this->render('modifyData/ModifyMacrosTemplate.twig', [
            'form' => $form,
            'page_title' => 'Add macro-nutrient intake - SMC',
            'page_intent' => 'Add macro-nutrient count'
        ]);
    }

    #[Route(['/reduceMacros', '/reducemacros'], name: 'reduceMacros', methods: ['GET', 'POST'])]
    public function reduceMacros(Request $request): Response {
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw $this->createAccessDeniedException('User not found');
        }

        $macroDTO = new MacroDataDTO(0, 0, 0, 0, 0);

        $form = $this->createForm(ModifyMacrosType::class, $macroDTO);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var MacroDataDTO $data */
            $data = $form->getData();

            $data->setCalories(
                $data->getProtein() * 4 +
                $data->getFats() * 9 +
                $data->getCarbs() * 4 +
                $data->getFiber() * 2
            );

            if ($this->macroIntakeUpdater->updateMacroIntake($user, $data, 'reduce')) {
                $this->addFlash('successMessages', 'Successfully reduced the macro-nutrient count!');
                
                return $this->redirectToRoute('home');
            }
        }

        return $this->render('modifyData/ModifyMacrosTemplate.twig', [
            'form' => $form,
            'page_title' => 'Reduce macro-nutrient intake - SMC',
            'page_intent' => 'Reduce macro-nutrient count'
        ]);
    }
}

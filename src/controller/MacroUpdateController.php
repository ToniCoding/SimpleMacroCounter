<?php

namespace src\Controller;

use src\DTO\MacroDataDTO;
use src\Entity\KcalsDaily;
use src\Entity\User;
use src\Form\AddMacrosType;
use src\Service\MacroIntakeUpdater;

use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Log\LoggerInterface;

class MacroUpdateController extends AbstractController {
    public function __construct(
        private EntityManagerInterface $entityManager,
        private MacroIntakeUpdater $macroIntakeUpdater,
        private LoggerInterface $logger
    ) {}

    #[Route(['/modifyMacros', '/modifymacros'], name: 'modifyMacros', methods: ['GET', 'POST'])]
    public function modifyMacros(Request $request): Response {
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw $this->createAccessDeniedException('User not found');
        }

        $macroDTO = new MacroDataDTO(0, 0, 0, 0);
        $form = $this->createForm(AddMacrosType::class, $macroDTO);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var MacroDataDTO $data */
            $data = $form->getData();

            if ($this->macroIntakeUpdater->updateMacroIntake($user, $data)) {
                $this->addFlash('successMessages', 'Correct');
                
                return $this->redirectToRoute('home');
            }
        }

        return $this->render('modifyData/AddMacrosTemplate.twig', [
            'form' => $form
        ]);
    }
}

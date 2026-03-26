<?php

namespace src\Controller;

use src\Helpers\DateParser;
use src\Service\UserMacrosRetrieve;
use src\Entity\User;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Component\Routing\Annotation\Route;

class HistoryPageController extends AbstractController {
    public function __construct(
        private UserMacrosRetrieve $userMacrosRetrieve,
        private DateParser $dateParser
        ){}

    #[Route(['/history'], name: 'history')]
    public function history(Request $request): Response {
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw $this->createAccessDeniedException('User not found');
        }

        $numberOfDays = $request->query->get('lastDays', 7);

        return $this->render('HistoryPageTemplate.twig', [
            'user' => $user->getUsername(),
            'page_title' => 'History - SMC',
            'historyData' => $this->getLastDaysHistory($numberOfDays),
            'days' => $numberOfDays
        ]);
    }

    private function getLastDaysHistory(int $numberOfDays): array {
        if ($numberOfDays <= 0) $numberOfDays = 7;
        if ($numberOfDays > 180) $numberOfDays = 180;
        
        return $this->userMacrosRetrieve->getDataFromPreviousDays($this->getUser(), $numberOfDays);
    }
}

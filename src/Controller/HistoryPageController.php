<?php

namespace src\Controller;

use src\Helpers\DateParser;
use src\Service\UserMacrosRetrieve;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Component\Routing\Annotation\Route;

class HistoryPageController extends AbstractController {
    public function __construct(
        private UserMacrosRetrieve $userMacrosRetrieve,
        private DateParser $dateParser
    ) {}

    #[Route(['/history'], name: 'history', methods: 'GET')]
    public function history(Request $request): Response {
        $this->isGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        $numberOfDays = (int) $request->query->get('lastDays', 7);

        return $this->render('HistoryPageTemplate.twig', [
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

<?php

namespace src\Controller;

use src\Helpers\DateParser;
use src\Service\UserMacrosRetrieve;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Component\Routing\Annotation\Route;

class HistoryPageController extends AbstractController {
    private int $defaultHistoryDays;
    private int $maxHistoryDays;

    public function __construct(
        private UserMacrosRetrieve $userMacrosRetrieve,
        private DateParser $dateParser,
        private ParameterBagInterface $params
    ) {
        $this->defaultHistoryDays = $this->params->get('history.default_shown_days');
        $this->maxHistoryDays = $this->params->get('history.max_shown_days');
    }

    #[Route(['/history'], name: 'history', methods: 'GET')]
    public function history(Request $request): Response {
        $defaultHistoryDays = $this->params->get('history.default_shown_days');
        $numberOfDays = (int) $request->query->get('lastDays', $defaultHistoryDays);

        return $this->render('HistoryPageTemplate.twig', [
            'page_title' => 'History - SMC',
            'historyData' => $this->getLastDaysHistory($numberOfDays),
            'days' => $numberOfDays
        ]);
    }

    private function getLastDaysHistory(int $numberOfDays): array {
        if ($numberOfDays <= $this->defaultHistoryDays) $numberOfDays = $this->defaultHistoryDays;
        if ($numberOfDays > $this->maxHistoryDays) $numberOfDays = $this->maxHistoryDays;

        return $this->userMacrosRetrieve->getDataFromPreviousDays($this->getUser(), $numberOfDays);
    }
}

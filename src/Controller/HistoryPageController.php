<?php

namespace App\Controller;

use App\Helpers\DateParser;
use App\Service\MacrosRetrieveService;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller responsible for rendering and managing the user's macronutrient history page.
 */
class HistoryPageController extends AbstractController {
    private int $defaultHistoryDays;
    private int $maxHistoryDays;

    /**
     * Initializes the controller with necessary services and parameter configurations.
     * 
     * @param MacrosRetrieveService $macrosRetrieveService Service to retrieve historical macro records.
     * @param DateParser $dateParser Helper to parse and format dates.
     * @param ParameterBagInterface $params Parameter bag to access application configuration.
     */
    public function __construct(
        private MacrosRetrieveService $macrosRetrieveService,
        private DateParser $dateParser,
        private ParameterBagInterface $params
    ) {
        $this->defaultHistoryDays = $this->params->get('history.default_shown_days');
        $this->maxHistoryDays = $this->params->get('history.max_shown_days');
    }

    /**
     * Renders the history page displaying the user's macronutrient intake over a specified number of days.
     * 
     * @param Request $request The incoming HTTP request containing query parameters.
     * @return Response Returns the rendered history page template.
     */
    #[Route(['/history'], name: 'history', methods: 'GET')]
    public function history(Request $request): Response {
        $defaultHistoryDays = $this->params->get('history.default_shown_days');
        $numberOfDays = (int) $request->query->get('lastDays', $defaultHistoryDays);

        return $this->render('HistoryPageTemplate.twig.html', [
            'page_title' => 'History - SMC',
            'historyData' => $this->getLastDaysHistory($numberOfDays),
            'days' => $numberOfDays
        ]);
    }

    /**
     * Retrieves and filters the historical macro data for a validated range of previous days.
     * 
     * @param int $numberOfDays The requested number of days for history retrieval.
     * @return array Returns an array containing the historical macro data records.
     */
    private function getLastDaysHistory(int $numberOfDays): array {
        if ($numberOfDays <= $this->defaultHistoryDays) $numberOfDays = $this->defaultHistoryDays;
        if ($numberOfDays > $this->maxHistoryDays) $numberOfDays = $this->maxHistoryDays;

        return $this->macrosRetrieveService->getDataFromPreviousDays($this->getUser(), $numberOfDays);
    }
}

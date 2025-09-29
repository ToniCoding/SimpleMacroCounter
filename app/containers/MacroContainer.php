<?php

namespace App\Containers;

use Config\Container;

use App\Controller\CombinedMacroController;
use App\Model\CombinedMacros;
use App\Repository\{ UserGoalsRepository, CaloriesIntakeRepository };
use App\Helpers\DateParser;
use App\View\MacroCounterView;

use PDO;

class MacroContainer extends Container {
    private Container $globalContainer;
    private Container $macroServiceContainer;

    private MacroCounterView $macroCounterView;
    private CaloriesIntakeRepository $caloriesIntakeRepository;
    private UserGoalsRepository $userGoalsRepository;
    private CombinedMacroController $combinedMacroController;

    private PDO $dbConnection;
    private DateParser $dateParser;

    public function __construct(Container $globalContainer) {
        $this->globalContainer = $globalContainer;
        $this->dbConnection = $this->globalContainer->getService('db')->connect();
        $this->dateParser = $this->globalContainer->getService('dateParser');

        $this->setService('macroCounterView', function(): MacroCounterView {
            return new MacroCounterView();
        });

        $this->setService('combinedMacros', function(): CombinedMacros {
            $initialData = [
                'protein' => [0, 150],
                'carbs' => [0, 250],
                'fats' => [0, 70]
            ];

            return new CombinedMacros($initialData);
        });

        $this->setService('combinedMacroController', function($c) use ($globalContainer): CombinedMacroController {
            $combinedMacros = $c->getService('combinedMacros');
            $caloriesRepo = $globalContainer->getService('caloriesIntakeRepository');
            $userGoalsRepo = $globalContainer->getService('userGoalsRepository');
            $macroCounterView = $c->getService('macroCounterView');
            $dateParser = $globalContainer->getService('dateParser');

            return new CombinedMacroController($combinedMacros, $caloriesRepo, $userGoalsRepo, $dateParser, $macroCounterView);
        });

        $this->macroCounterView = $this->getService('macroCounterView');
        $this->caloriesIntakeRepository = $globalContainer->getService('caloriesIntakeRepository');
        $this->userGoalsRepository = $globalContainer->getService('userGoalsRepository');
    }

    public function getCombinedMacroController(): CombinedMacroController {
        return $this->getService('combinedMacroController');
    }
}

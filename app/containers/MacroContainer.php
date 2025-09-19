<?php

class MacroContainer extends Container {
    private Container $globalContainer;
    private Container $macroServiceContainer;

    private MacroCounterView $macroCounterView;
    private CaloriesIntakeRepository $caloriesIntakeRepository;
    private UserGoalsRepository $userGoalsRepository;
    private MacroController $macroController;
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

        $this->setService('caloriesIntakeRepository', function(): CaloriesIntakeRepository {
            return new CaloriesIntakeRepository($this->dbConnection, $this->dateParser);
        });

        $this->setService('userGoalsRepository', function(): UserGoalsRepository {
            return new UserGoalsRepository($this->dbConnection);
        });

        $this->setService('macroController', function(): MacroController {
            $defaultMacro = new Macro("protein", 0, 150);
            return new MacroController(
            $defaultMacro,
            $this->getService('macroCounterView'),
            $this->getService('caloriesIntakeRepository'),
            $this->getService('userGoalsRepository')
            );
        });

        $this->setService('combinedMacros', function(): CombinedMacros {
            $initialData = [
                'protein' => [0, 150],
                'carbs' => [0, 250],
                'fats' => [0, 70]
            ];

            return new CombinedMacros($initialData);
        });

        $this->setService('combinedMacroController', function($c): CombinedMacroController {
            $combinedMacros = $c->getService('combinedMacros');
            $caloriesRepo = $c->getService('caloriesIntakeRepository');
            $userGoalsRepo = $c->getService('userGoalsRepository');
            $macroCounterView = $c->getService('macroCounterView');
            return new CombinedMacroController($combinedMacros, $caloriesRepo, $userGoalsRepo, $macroCounterView);
        });

        $this->macroCounterView = $this->getService('macroCounterView');
        $this->caloriesIntakeRepository = $this->getService('caloriesIntakeRepository');
        $this->userGoalsRepository = $this->getService('userGoalsRepository');
    }

    public function getMacroController(): MacroController {
        return $this->getService('macroController');
    }

    public function getCombinedMacroController(): CombinedMacroController {
        return $this->getService('combinedMacroController');
    }
}

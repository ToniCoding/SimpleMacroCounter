<?php

class MacroContainer extends Container {
    private Container $globalContainer;
    private Container $macroServiceContainer;

    private MacroCounterView $macroCounterView;
    private CaloriesIntakeRepository $caloriesIntakeRepository;
    private UserGoalsRepository $userGoalsRepository;
    private MacroController $macroController;

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

        $this->setService('mockMacro', function(string $macroName, int $qty, int $goal): Macro {
            return new Macro($macroName, $qty, $goal);
        });

        $this->setService('macroController', function($macroServiceContainer){
            return new MacroController(
            new Macro("protein", 0, 0),
            $macroServiceContainer->getService('macroCounterView'),
            $macroServiceContainer->getService('caloriesIntakeRepository'),
            $macroServiceContainer->getService('userGoalsRepository')
            );
        });

        $this->macroCounterView = $this->getService('macroCounterView');
        $this->caloriesIntakeRepository = $this->getService('caloriesIntakeRepository');
        $this->userGoalsRepository = $this->getService('userGoalsRepository');
        $this->macroController = $this->getService('macroController');
    }

    public function getMacroController(): MacroController {
        return $this->macroController;
    }
}

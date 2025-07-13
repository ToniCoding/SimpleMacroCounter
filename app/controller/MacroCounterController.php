<?php

require_once "app/model/MacrosCounter.php";
require_once "app/view/MacroCounterView.php";
require_once "app/helpers/htmlHelper.php";

class MacroCounterController {
    private MacroCounterView $macroView;

    public function __construct() {
        $this->macroView = new MacroCounterView();
    }

    public function createMacros(array $macroName, array $macroCount, array $macroGoals): MacroCounter {
        return new MacroCounter($macroName, $macroCount, $macroGoals);
    }

    public function calculateTotalCalories(MacroCounter $macroCounter): int {
        return $macroCounter->calculateCalories();
    }

    public function showMacrosAndCalories(MacroCounter $macroCounter): void {
        $macrosList = $macroCounter->buildAssociativeArrayWithMacros();
        $caloiesConsumed = $macroCounter->calculateCalories();

        echo $this->macroView->displayMacrosAndCalories($macrosList, $caloiesConsumed);
    }
}

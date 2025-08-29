<?php

require_once __DIR__ . "../../AppConstants.php";

require_once BASE_PATH . "config.php";
require_once BASE_PATH . "app/model/MacrosCounter.php";
require_once BASE_PATH . "app/view/MacroCounterView.php";
require_once BASE_PATH . "app/helpers/htmlHelper.php";

/**
 * Class MacroCounterController
 * Controller that manages the interaction between the MacroCounter model and the MacroCounterView.
 */
class MacroCounterController {
    private MacroCounterView $macroView;

    /**
     * Constructor that initializes the view.
     */
    public function __construct(MacroCounterView $macroCounterView) {
        $this->macroView = $macroCounterView;
    }

    /**
     * Creates a MacroCounter instance with the provided data.
     *
     * @param array $macroName Names of the macros.
     * @param array $macroCount Amounts of each macro.
     * @param array $macroGoals Goals for each macro.
     * @return MacroCounter New instance of MacroCounter.
     */
    public function createMacros(array $macroName, array $macroCount, array $macroGoals): MacroCounter {
        return new MacroCounter($macroName, $macroCount, $macroGoals);
    }

    /**
     * Calculates the total calories using the model.
     *
     * @param MacroCounter $macroCounter Model instance.
     * @return int Total calculated calories.
     */
    public function calculateTotalCalories(MacroCounter $macroCounter): int {
        return $macroCounter->calculateCalories();
    }

    /**
     * Displays macros and calories through the view.
     *
     * @param MacroCounter $macroCounter Model instance.
     */
    public function displayMacrosAndCalories(MacroCounter $macroCounter): void {
        $macrosList = $macroCounter->buildAssociativeArrayWithMacros();
        $caloriesConsumed = $macroCounter->calculateCalories();
        echo $this->macroView->displayMacrosAndCalories($macrosList, $caloriesConsumed);
    }

    /**
     * Displays the form to input macros.
     */
    public function displayIngestedMacrosForm(): void {
        echo $this->macroView->displayIngestedMacrosForm();
    }

    /**
     * Displays the current date using the view.
     */
    public function displayDate(): void {
        echo $this->macroView->displayDate();
    }
}

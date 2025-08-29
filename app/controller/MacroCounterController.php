<?php

/**
 * Class MacrosCounterController
 * Controller that manages the interaction between the MacrosCounter model and the MacrosCounterView.
 */
class MacrosCounterController {
    private MacroCounterView $macroView;

    /**
     * Constructor that initializes the view.
     */
    public function __construct(MacroCounterView $MacrosCounterView) {
        $this->macroView = $MacrosCounterView;
    }

    /**
     * Creates a MacrosCounter instance with the provided data.
     *
     * @param array $macroName Names of the macros.
     * @param array $macroCount Amounts of each macro.
     * @param array $macroGoals Goals for each macro.
     * @return MacrosCounter New instance of MacrosCounter.
     */
    public function createMacros(array $macroName, array $macroCount, array $macroGoals): MacrosCounter {
        return new MacrosCounter($macroName, $macroCount, $macroGoals);
    }

    /**
     * Calculates the total calories using the model.
     *
     * @param MacrosCounter $MacrosCounter Model instance.
     * @return int Total calculated calories.
     */
    public function calculateTotalCalories(MacrosCounter $MacrosCounter): int {
        return $MacrosCounter->calculateCalories();
    }

    /**
     * Displays macros and calories through the view.
     *
     * @param MacrosCounter $MacrosCounter Model instance.
     */
    public function displayMacrosAndCalories(MacrosCounter $MacrosCounter): void {
        $macrosList = $MacrosCounter->buildAssociativeArrayWithMacros();
        $caloriesConsumed = $MacrosCounter->calculateCalories();
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

<?php

require_once "app/model/MacrosCounter.php";

class MacroCounterController {
    public function createMacros(array $macroName, array $macroCount, array $macroGoals): MacroCounter {
        return new MacroCounter($macroName, $macroCount, $macroGoals);
    }

    public function calculateTotalCalories(MacroCounter $macroCounter): int {
        return $macroCounter->calculateCalories();
    }

    public function showMacrosAndCalories(MacroCounter $macroCounter): void {
        $macrosList = $macroCounter->buildAssociativeArrayWithMacros();
        $calories = $macroCounter->calculateCalories();

        echo "Macros and Goals:\n";
        foreach ($macrosList as $macroName => $data) {
            echo "<p>{$macroName}: Count = {$data['count']}, Goal = {$data['goal']}<p>\n";
        }
        echo "Total Calories: {$calories}\n";
    }
}

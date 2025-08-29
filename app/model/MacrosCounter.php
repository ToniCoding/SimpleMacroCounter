<?php

/**
 * MacroCounter class managing multiple macros and calculating total calories.
 */
class MacrosCounter {
    private array $macrosNames = [];
    private array $macroCounts = [];
    private array $macroGoals = [];
    private int $calories;

    public function __construct(array $macrosNames, array $macroCounts, array $macroGoals) {
        $this->macrosNames = $macrosNames;
        $this->macroCounts = $macroCounts;
        $this->macroGoals = $macroGoals;
        $this->calories = 0;
    }

    /**
     * Builds an associative array with macros data.
     *
     * @return array Associative array with keys: name, count and goal.
     */
    public function buildAssociativeArrayWithMacros(): array {
        $assocMacros = [];

        for ($i = 0; $i < count($this->macrosNames); $i++) {
            $assocMacros[$this->macrosNames[$i]] = [
                'count' => $this->macroCounts[$i],
                'goal' => $this->macroGoals[$i]
            ];
        }

        return $assocMacros;
    }

    /**
     * Calculates the total calories based on macros.
     *
     * @return int Total calories.
     */
    public function calculateCalories(): int {
        $calories = 0;
        $macrosList = $this->buildAssociativeArrayWithMacros();

        foreach($macrosList as $macroName => $macroData) {
            switch ($macroName) {
                case 'protein':
                    $calories += $macroData['count'] * 4;
                    break;

                case 'carbs':
                    $calories += $macroData['count'] * 4;
                    break;

                case 'fat':
                    $calories += $macroData['count'] * 9;
                    break;
            }
        }

        return $calories;
    }
}

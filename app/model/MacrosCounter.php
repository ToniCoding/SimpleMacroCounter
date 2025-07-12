<?php

class MacroCounter {
    private $macrosNames = [];
    private $macroCounts = [];
    private $macroGoals = [];
    private $calories;

    public function __construct(array $macrosNames, array $macroCounts, array $macroGoals) {
        $this->macrosNames = $macrosNames;
        $this->macroCounts = $macroCounts;
        $this->macroGoals = $macroGoals;
        $this->calories = 0;
    }

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
            }
        }

        return $calories;
    }
}
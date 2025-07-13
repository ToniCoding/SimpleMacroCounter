<?php

class CalorieCalculator {
    private $protein;
    private $carbs;
    private $fat;

    public function calculateCalories(int $protein, int $carbs, int $fat): int {
        return $protein * 4 + $carbs * 4 + $fat * 9;
    }
}

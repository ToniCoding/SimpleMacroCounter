<?php

function calculateCalorieIntake(array $caloriesConsumed): int {
    $totalCalories = 0;

    foreach ($caloriesConsumed as $macroName => $macroConsumed) {
        switch($macroName) {
            case 'protein':
            case 'carbs':
                $totalCalories += $macroConsumed * 4;
                break;
            case 'fats':
                $totalCalories += $macroConsumed * 9;
                break;
        }
    }

    return $totalCalories;
}

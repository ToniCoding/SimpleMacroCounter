<?php

/**
 * Class MacrosCounterController
 *
 * Handles the interaction between the Macro model and the MacroCounterView,
 * coordinating business logic and data flow for macronutrient tracking.
 */

class MacroController {
    private Macro $macro;
    private MacroCounterView $macroView;
    private CaloriesIntakeRepository $caloriesIntakeRepository;
    private UserGoalsRepository $userGoalsRepository;

    /**
    * Initializes a new instance of the class with required dependencies.
    *
    * @param Macro                    $macro                   The Macro model instance (not stored, but can be used for setup or validation).
    * @param MacroCounterView         $macrosCounterView       The view responsible for rendering the macro counter UI.
    * @param CaloriesIntakeRepository $caloriesIntakeRepository Repository for handling daily calorie and macronutrient data operations.
    */
    public function __construct(Macro $macro, MacroCounterView $MacrosCounterView, CaloriesIntakeRepository $caloriesIntakeRepository, UserGoalsRepository $userGoalsRepository) {
        $this->macro = $macro;
        $this->macroView = $MacrosCounterView;
        $this->caloriesIntakeRepository = $caloriesIntakeRepository;
        $this->userGoalsRepository = $userGoalsRepository;
    }

    /**
    * Increments the specified macronutrient value for a given user.
    *
    * @param int    $userId The ID of the user whose macronutrient record will be updated.
    * @param string $macro  The macronutrient to increment. Allowed values: "protein", "carbs", "fats".
    * @param int    $amount The amount to add to the current macronutrient value (in grams).
    *
    * @return bool  True on successful update, false if the macronutrient is invalid or the update fails.
    */
    public function addMacros(int $userId, string $macro, int $amount): bool {
        $allowedMacroNutrients = ["protein", "carbs", "fats"];

        if (in_array($macro, $allowedMacroNutrients, true)) {
            return $this->caloriesIntakeRepository->addMacroNutrient($userId, $macro, $amount);
        }

        return false;
    }

    public function calculateCalorieIntake(int $userId): int {
        $macros = $this->caloriesIntakeRepository->getMacros($userId);
        $totalCalories = 0;

        foreach($macros as $macro => $amount) {
            switch($macro) {
                case 'protein':
                case 'carbs':
                    $totalCalories += $amount * 4;
                    break;
                case 'fats':
                    $totalCalories += $amount * 9;
                    break;
            }
        }

        return $totalCalories;
    }
    
    private function formMacrosDataArray(array $allMacros, array $macrosGoals): array {
        $macrosData = [];

        foreach ($allMacros as $macro => $amount) {
            $goal = $macrosGoals[$macro] ?? 0;

            $macrosData[$macro] = [
                "amount" => $amount,
                "goal" => $goal
            ];
        }

        return $macrosData;
    }
}

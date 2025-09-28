<?php

namespace App\Controller;

use App\Model\CombinedMacros;
use App\Model\Macro;
use App\Repository\CaloriesIntakeRepository;
use App\Repository\UserGoalsRepository;
use App\Helpers\DateParser;
use App\View\MacroCounterView;

use InvalidArgumentException;

class CombinedMacroController {
    private CombinedMacros $combinedMacros;
    private CaloriesIntakeRepository $caloriesRepo;
    private UserGoalsRepository $userGoalsRepository;
    private MacroCounterView $macroCounterView;
    private DateParser $dateParser;
    private array $allowedMacros = ['protein', 'carbs', 'fats'];

    public function __construct(CombinedMacros $combinedMacros, CaloriesIntakeRepository $caloriesRepo, UserGoalsRepository $userGoalsRepository, DateParser $dateParser, MacroCounterView $macroCounterView) {
        $this->combinedMacros = $combinedMacros;
        $this->caloriesRepo = $caloriesRepo;
        $this->userGoalsRepository = $userGoalsRepository;
        $this->dateParser = $dateParser;
        $this->macroCounterView = $macroCounterView;
    }

    public function getMacroData(int $userId): array {
        $userMacroData = $this->caloriesRepo->getMacros($userId);
        $this->combinedMacros->setMacrosData($userMacroData);
        return $this->combinedMacros->getMacrosData();
    }

    public function getMacroGoal(int $userId): array {
        $userMacroGoals = $this->userGoalsRepository->getUserGoals($userId);
        $this->combinedMacros->setMacrosData($userMacroGoals);
        return $this->combinedMacros->getMacrosData();
    }

    public function getSpecificMacro(string $macroName): Macro {
        if (!in_array($macroName, $this->allowedMacros)) {
            throw new InvalidArgumentException('The selected macro is not allowed.');
        }
        return $this->combinedMacros->getSpecificMacro($macroName);
    }

    public function setSpecificMacro(Macro $macro): void {
        if (!in_array($macro->getMacroName(), $this->allowedMacros)) {
            throw new InvalidArgumentException('The selected macro is not allowed.');
        }
        $this->combinedMacros->setSpecificMacro($macro);
    }

    public function getConsumedCalories(int $userId): int {
        $currentDate = $this->dateParser->getDate('Y:m:d');
        $consumedMacros = $this->caloriesRepo->getTodaysRegisteredData($userId, $currentDate);
        $consumedCalories = calculateCalorieIntake($consumedMacros);

        return $consumedCalories;
    }

    public function displayMacrosTable(array $consumedMacros, array $goalMacros, int $userId): void {
        $consumedCalories = $this->getConsumedCalories($userId);
        $this->macroCounterView->renderMacrosTable($consumedMacros, $goalMacros);
        $this->macroCounterView->renderTotalMacros($consumedCalories);
    }
}

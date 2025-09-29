<?php

namespace App\Controller;

use App\Model\{ Macro, CombinedMacros };
use App\Repository\{ CaloriesIntakeRepository, UserGoalsRepository };
use App\Helpers\DateParser;
use App\View\MacroCounterView;
use App\Logging\Logger;
use InvalidArgumentException;

class CombinedMacroController {
    private CombinedMacros $combinedMacros;
    private CaloriesIntakeRepository $caloriesRepo;
    private UserGoalsRepository $userGoalsRepository;
    private MacroCounterView $macroCounterView;
    private DateParser $dateParser;
    private Logger $log;
    private array $allowedMacros = ['protein', 'carbs', 'fats'];

    public function __construct(CombinedMacros $combinedMacros, CaloriesIntakeRepository $caloriesRepo, UserGoalsRepository $userGoalsRepository, DateParser $dateParser, MacroCounterView $macroCounterView, Logger $log) {
        $this->combinedMacros = $combinedMacros;
        $this->caloriesRepo = $caloriesRepo;
        $this->userGoalsRepository = $userGoalsRepository;
        $this->dateParser = $dateParser;
        $this->macroCounterView = $macroCounterView;
        $this->log = $log;
    }

    public function getMacroData(int $userId): array {
        $this->log->info("Execute get macro data for user $userId.");
        $userMacroData = $this->caloriesRepo->getMacros($userId);
        $this->combinedMacros->setMacrosData($userMacroData);
        return $this->combinedMacros->getMacrosData();
    }

    public function getMacroGoal(int $userId): array {
        $this->log->info("Execute get macro goal for user $userId.");
        $userMacroGoals = $this->userGoalsRepository->getUserGoals($userId);
        $this->combinedMacros->setMacrosData($userMacroGoals);
        return $this->combinedMacros->getMacrosData();
    }

    public function getSpecificMacro(string $macroName): Macro {
        if (!in_array($macroName, $this->allowedMacros)) {
            $this->log->error("The selected is not allowed. User selected: $macroName");
            throw new InvalidArgumentException('The selected macro is not allowed.');
        }
        return $this->combinedMacros->getSpecificMacro($macroName);
    }

    public function setSpecificMacro(Macro $macro): void {
        if (!in_array($macro->getMacroName(), $this->allowedMacros)) {
            $this->log->error('The selected is not allowed. User selected: ' . $macro->getMacroName());
            throw new InvalidArgumentException('The selected macro is not allowed.');
        }
        $this->combinedMacros->setSpecificMacro($macro);
    }

    public function getConsumedCalories(int $userId): int {
        $this->log->info("Execute get consumed calories for user $userId.");
        $currentDate = $this->dateParser->getDate('Y:m:d');
        $consumedMacros = $this->caloriesRepo->getTodaysRegisteredData($userId, $currentDate);
        $consumedCalories = calculateCalorieIntake($consumedMacros);

        return $consumedCalories;
    }

    public function displayMacrosTable(array $consumedMacros, array $goalMacros, int $userId): void {
        $this->log->info('Try displaying macros table.');
        $consumedCalories = $this->getConsumedCalories($userId);
        $this->macroCounterView->renderMacrosTable($consumedMacros, $goalMacros);
        $this->macroCounterView->renderTotalMacros($consumedCalories);
    }
}

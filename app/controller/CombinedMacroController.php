<?php

class CombinedMacroController {
    private CombinedMacros $combinedMacros;
    private CaloriesIntakeRepository $caloriesRepo;
    private UserGoalsRepository $userGoalsRepository;
    private MacroCounterView $macroCounterView;
    private array $allowedMacros = ['protein', 'carbs', 'fats'];

    public function __construct(CombinedMacros $combinedMacros, CaloriesIntakeRepository $caloriesRepo, UserGoalsRepository $userGoalsRepository, MacroCounterView $macroCounterView) {
        $this->combinedMacros = $combinedMacros;
        $this->caloriesRepo = $caloriesRepo;
        $this->userGoalsRepository = $userGoalsRepository;
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
            throw new InvalidArgumentException("The selected macro is not allowed.");
        }
        return $this->combinedMacros->getSpecificMacro($macroName);
    }

    public function setSpecificMacro(Macro $macro): void {
        if (!in_array($macro->getMacroName(), $this->allowedMacros)) {
            throw new InvalidArgumentException("The selected macro is not allowed.");
        }
        $this->combinedMacros->setSpecificMacro($macro);
    }

    public function displayMacrosTable(array $consumedMacros, array $goalMacros): void {
        $this->macroCounterView->renderMacrosTable($consumedMacros, $goalMacros);
    }
}

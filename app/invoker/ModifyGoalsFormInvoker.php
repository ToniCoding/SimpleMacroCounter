<?php

class ModifyGoalsFormInvoker {
    private UserRepository $userRepository;
    private UserGoalsRepository $userGoalsRepository;

    public function __construct(UserRepository $userRepository, UserGoalsRepository $userGoalsRepository) {
        $this->userRepository = $userRepository;
        $this->userGoalsRepository = $userGoalsRepository;
    }

    public function handleModGoalsData(array $postData) {
        $macroName = filter_var(trim($postData["macroName"]), FILTER_SANITIZE_STRING) ?? '';
        $macroGoal = filter_var(trim($postData["macroGoal"]), FILTER_SANITIZE_STRING) ?? '';
        $macroComposed = $this->buildNewMacroObject($macroName, 0, $macroGoal);
        $username = $this->userRepository->getByAuthToken($_COOKIE['auth_token']);
        $userId = $this->userRepository->findUserIdByName($username['username'])[0]['id'];

        if (strlen($macroGoal) > 4) {
            throw new LengthException("Number length cannot be longer than 4 characters.");
        }
        
        return $this->updateMacroGoal($macroComposed, $userId);
    }

    private function buildNewMacroObject(string $macroName, int $consumedQty, int $macroGoal): Macro {
        $allowedMacros = ["protein", "carbs", "fats"];
        if (!in_array($macroName, $allowedMacros)) {
            throw new InvalidArgumentException("Macro not allowed");
        }

        return new Macro($macroName, $consumedQty, $macroGoal);
    }

    private function updateMacroGoal(Macro $macro, int $userId): bool {
        return $this->userGoalsRepository->setUserGoal($userId, $macro);
    }
}
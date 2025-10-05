<?php

namespace App\Invoker;

use App\Model\Macro;
use App\Repository\{ UserRepository, CaloriesIntakeRepository, UserGoalsRepository };
use App\Exceptions\ExceededMacroLimitException;

use Exception, LengthException, InvalidArgumentException;

class ModifyGoalsFormInvoker {
    private UserRepository $userRepository;
    private CaloriesIntakeRepository $caloriesIntakeRepository;
    private UserGoalsRepository $userGoalsRepository;

    public function __construct(UserRepository $userRepository, CaloriesIntakeRepository $caloriesIntakeRepository, UserGoalsRepository $userGoalsRepository) {
        $this->userRepository = $userRepository;
        $this->caloriesIntakeRepository = $caloriesIntakeRepository;
        $this->userGoalsRepository = $userGoalsRepository;
    }

    /**
     * Handles the data coming from the update macronutrient goal.
     * @param array $postData
     * @throws \LengthException
     * @throws \App\Exceptions\ExceededMacroLimitException
     * @return bool
     */
    public function handleModGoalsData(array $postData): bool {
        $macroName = filter_var(trim($postData['macroName']), FILTER_SANITIZE_STRING) ?? '';
        $macroGoal = filter_var(trim($postData['macroGoal']), FILTER_SANITIZE_STRING) ?? '';
        $macroComposed = $this->buildNewMacroObject($macroName, 0, $macroGoal);
        $username = $this->userRepository->getByAuthToken($_COOKIE['auth_token']);
        $userId = $this->userRepository->findUserIdByName($username['username'])[0]['id'];

        if (strlen($macroGoal) >= 4) {
            throw new LengthException('Number length cannot be longer than 4 characters.');
        }

        if ((int)$macroGoal > 400) {
            throw new ExceededMacroLimitException("We doubt you can consume more than 500 of {$macroName} ;(");
        }
        
        return $this->updateMacroGoal($macroComposed, $userId);
    }

    /**
     * Handles the data coming from the update macronutrient consumed form.
     * @param array $postData
     * @throws \LengthException
     * @throws \Exception
     * @throws \App\Exceptions\ExceededMacroLimitException
     * @return bool
     */
    public function handleMacroConsumed(array $postData): bool {
        $username = $this->userRepository->getByAuthToken($_COOKIE['auth_token']);
        $userId = $this->userRepository->findUserIdByName($username['username'])[0]['id'];
        
        $proteins = $postData['proteins'] ?? '';
        $carbs = $postData['carbs'] ?? '';
        $fats = $postData['fats'] ?? '';

        if (strlen($proteins) >= 4 && strlen($carbs) >= 4 && strlen($fats) >= 4) {
            throw new LengthException('Macro-nutrient quantities cannot exceed 500.');
        }

        $proteinsQty = (int)filter_var($proteins ?? null, FILTER_SANITIZE_NUMBER_INT);
        $carbsQty = (int)filter_var($carbs ?? null, FILTER_SANITIZE_NUMBER_INT);
        $fatsQty = (int)filter_var($fats ?? null, FILTER_SANITIZE_NUMBER_INT);

        if (!is_numeric($proteinsQty) || !is_numeric($carbsQty) || !is_numeric($fatsQty)) {
            throw new Exception('The prompted amounts are not numeric.');
        }

        if ($proteinsQty > 400 || $carbsQty > 400 || $fatsQty > 400) {
            throw new ExceededMacroLimitException('Are you sure you consumed more than 500 in a day?');
        }

        $macrosNumber = [
            'protein' => $proteinsQty,
            'carbs' => $carbsQty,
            'fats' => $fatsQty
        ];

        $sqlSetPart = [];
        $params = [":userId" => $userId];

        foreach ($macrosNumber as $macroName => $macroAmount) {
            if ($macroAmount !== 0) {
                $sqlSetPart[] = "$macroName = $macroName + :$macroName";
                $params[":$macroName"] = $macroAmount;
            }
        }

        if (empty($sqlSetPart)) {
            return false;
        }

        return $this->caloriesIntakeRepository->updateMacroCount($params, implode(', ', $sqlSetPart));
    }

    /**
     * Builds the Macro object.
     * @param string $macroName
     * @param int $consumedQty
     * @param int $macroGoal
     * @throws \InvalidArgumentException
     * @return Macro
     */
    private function buildNewMacroObject(string $macroName, int $consumedQty, int $macroGoal): Macro {
        $allowedMacros = ['protein', 'carbs', 'fats'];
        if (!in_array($macroName, $allowedMacros)) {
            throw new InvalidArgumentException('Macro not allowed');
        }

        return new Macro($macroName, $consumedQty, $macroGoal);
    }

    /**
     * Updates the macro goal in database.
     * @param \App\Model\Macro $macro
     * @param int $userId
     * @return bool
     */
    private function updateMacroGoal(Macro $macro, int $userId): bool {
        return $this->userGoalsRepository->setUserGoal($userId, $macro);
    }
}
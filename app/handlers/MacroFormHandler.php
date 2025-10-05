<?php

namespace App\Handlers;

use App\Model\Macro;
use App\Helpers\DateParser;

use Exception, InvalidArgumentException;

class MacroFormHandler {
    private readonly DateParser $dateParser;

    public function __construct(DateParser $dateParser) {
        $this->dateParser = $dateParser;
    }

    /**
     * Handles the POST data coming from the macronutrient update.
     * @param array $postData
     * @throws \Exception
     * @throws \InvalidArgumentException
     * @return Macro
     */
    public function handle(array $postData): Macro {
        $macroType = filter_var(trim($postData['macro'] ?? '', FILTER_SANITIZE_STRING));
        $macroAmount = filter_var(trim($postData['amount'] ?? '', FILTER_SANITIZE_STRING));
        $macroGoal = filter_var(trim($postData['goal'] ?? '', FILTER_SANITIZE_STRING));

        if (!$macroType || !$macroAmount) {
            throw new Exception('Macro and amount must have a value');
        }
        
        if (!$macroGoal) {
            switch($macroType) {
                case 'protein':
                    $macroGoal = 100;
                    break;
                case 'carbs':
                    $macroGoal = 210;
                    break;
                case 'fats':
                    $macroGoal = 50;
                    break;
                default:
                    throw new InvalidArgumentException('Unrecognized macro type.');
            }
        }

        return new Macro($macroType, $macroAmount, $macroGoal);
    }
}

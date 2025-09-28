<?php

/**
 * Class in charge of getting the POST data from a Macro update.
 * It has the ability to form a Macro from the prompted data.
 */
class MacroFormHandler {
    private readonly DateParser $dateParser;

    public function __construct(DateParser $dateParser) {
        $this->dateParser = $dateParser;
    }

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

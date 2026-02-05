<?php

namespace src\Model;

use src\Model\Macro;

/**
 * This class represents the combination of all macros. It works with an associative array.
 * 
 * Associative array representation: array = ["macroName" => ["macroAmount", "macroGoal"]];
 */
class CombinedMacros {
    private array $macrosData;
    private array $macrosGoals;

    public function __construct(array $macrosData) {
        $this->macrosData = $macrosData;
    }

    public function getMacrosData(): array {
        return $this->macrosData;
    }

    public function setMacrosData(array $macrosData): void {
        $this->macrosData = $macrosData;
    }

    public function getSpecificMacro(string $macroName): Macro {
        $macroAmount = $this->macrosData[$macroName][0];
        $macroGoal = $this->macrosData[$macroName][1];

        return new Macro($macroName, $macroAmount, $macroGoal);
    }

    public function setSpecificMacro(Macro $newMacro): void {
        $this->macrosData[$newMacro->getMacroName()] = [
            $newMacro->getMacroCount(),
            $newMacro->getMacroGoal()
        ];
    }
}

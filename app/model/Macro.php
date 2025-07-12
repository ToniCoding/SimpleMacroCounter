<?php

class MacroCounter {
    private string $macroName;
    private int $macroCount;
    private int $macroGoal;
    private int $calories;

    public function __construct(string $macroName, int $macroCount, int $macroGoal) {
        $this->macroName = $macroName;
        $this->macroCount = $macroCount;
        $this->macroGoal = $macroGoal;
    }

    public function getMacroName(): string {
        return $this->macroName;
    }

    public function setMacroName(string $macroName): void {
        $this->macroName = $macroName;
    }

    public function getMacroCount(): int {
        return $this->macroCount;
    }

    public function setMacroCount(int $macroCount): void {
        $this->macroCount = $macroCount;
    }

    public function getMacroGoal(): int {
        return $this->macroGoal;
    }

    public function setMacroGoal(int $macroGoal): void {
        $this->macroGoal = $macroGoal;
    }

    public function _toString(): string {
        return "MacroCounter: {$this->macroName}, Count: {$this->macroCount}, Goal: {$this->macroGoal}";
    }
}
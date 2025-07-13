<?php
    class MacroCounterView {
        public function displayMacrosAndCalories(array $macrosList, int $calories): string {
            $macroCounterResult = "";
            $macroCounterResult .= htag_p("Macros and Goals")
            . "<table><thead><tr><th>Macro name</th></th><th>Count</th><th>Goal</th></tr></thead><tbody>";

            foreach ($macrosList as $macroName => $data) {
                $macroCounterResult .= "<tr><td>{$macroName}</td></td><td>{$data['count']}</td><td>{$data['goal']}</td></tr>";
            }

            $macroCounterResult .= "<tbody></table>" . htag_p("Total Calories - {$calories}");

            return $macroCounterResult;
        }
    }

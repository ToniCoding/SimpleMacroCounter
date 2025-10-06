<?php

namespace App\View;

/**
 * Class MacroCounterView
 * Responsible for displaying macros and related information.
 */
class MacroCounterView {
    /**
     * Renders an HTML table of macros and their goals.
     *
     * @param array $macrosData Array in the format ["macroType" => int];
     * @return void
     */
    public function renderMacrosTable(array $macrosData, array $goalMacros): void {
        $htmlRender = '<table border="1" cellpadding="5" cellspacing="0">
        <thead>
            <tr>
                <th>Macronutrient</th>
                <th>Amount</th>
                <th>Goal</th>
            </tr>
        </thead>
        <tbody>';

        foreach ($macrosData as $macroType => $amount) {
            $goal = $goalMacros[$macroType] ?? 0;

            $htmlRender .= '<tr>
                <td>' . htmlspecialchars(ucfirst($macroType)) . '</td>
                <td>' . htmlspecialchars($amount) . '</td>
                <td>' . htmlspecialchars($goal) . '</td>
            </tr>';
        }

        $htmlRender .= '</tbody></table>';

        echo $htmlRender;
    }

    public function renderTotalMacros(int $consumedCalories): void {
        echo "Total calories consumed today: {$consumedCalories}";
    }
}

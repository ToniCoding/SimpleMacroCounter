<?php

/**
 * Class MacroCounterView
 * Responsible for displaying macros and related information.
 */
class MacroCounterView {
    /**
     * Renders an HTML table of macros and their goals.
     *
     * @param array $macrosData Array in the format ["macroType" => ["amount" => int, "goal" => int]]
     * @return void
     */
    public function renderMacrosTable(array $macrosData): void {
        $htmlRender = '<table border="1" cellpadding="5" cellspacing="0">
        <thead>
        <tr>
        <th>Macronutrient</th>
        <th>Amount</th>
        <th>Goal</th>
        </tr>
        </thead>
        <tbody>';

        foreach ($macrosData as $macroType => $values) {
            $htmlRender .= '<tr>
            <td>' . htmlspecialchars(ucfirst($macroType)) . '</td>
            <td>' . htmlspecialchars($values['amount']) . '</td>
            <td>' . htmlspecialchars($values['goal']) . '</td>
            </tr>';
        }

        $htmlRender .= '</tbody></table>';

        echo $htmlRender;
    }
}

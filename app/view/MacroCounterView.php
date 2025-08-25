<?php

require_once __DIR__ . "/../../config.php";

require_once BASE_PATH . 'app/helpers/htmlHelper.php';
require_once BASE_PATH . 'app/helpers/dateParser.php';

/**
 * Class MacroCounterView
 * Responsible for displaying macros and related information.
 */
class MacroCounterView {
    private object $dateParser;

    /**
     * Constructor initializes the date parser.
     */
    public function __construct() {
        $this->dateParser = new DateParser();
    }

    /**
     * Displays a table of macros and their calories.
     *
     * @param array $macrosList Associative array of macros with counts and goals.
     * @param int $calories Total calories.
     * @return string HTML content.
     */
    public function displayMacrosAndCalories(array $macrosList, int $calories): string {
        $result = "";
        $result .= htag_p("Macros and Goals")
            . "<table><thead><tr><th>Macro name</th><th>Count</th><th>Goal</th></tr></thead><tbody>";

        foreach ($macrosList as $macroName => $data) {
            $result .= "<tr><td>{$macroName}</td><td>{$data['count']}</td><td>{$data['goal']}</td></tr>";
        }

        $result .= "</tbody></table>" . htag_p("Total Calories - {$calories}");

        return $result;
    }

    /**
     * Displays a form for entering macros.
     *
     * @return string HTML form.
     */
    public function displayIngestedMacrosForm(): string {
        return generateForm(
            action: 'action.php',
            method: 'POST',
            inputLabels: ["Macro name", "Count", "Goal"],
            inputType: 'text'
        );
    }

    /**
     * Returns the current date as a string.
     *
     * @return string Date string.
     */
    public function displayDate(): string {
        return $this->dateParser->getDate();
    }
}

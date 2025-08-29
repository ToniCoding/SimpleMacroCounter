<?php

/**
 * Function that wraps a text inside a <p> tag.
 *
 * @param string $text Text to wrap.
 * @return string Text wrapped in <p>.
 */
function htag_p(string $text): string {
    return "<p>{$text}</p>";
}

/**
 * Generates an HTML form with label and input elements.
 *
 * @param string $action URL where the form will be submitted.
 * @param string $method HTTP method (default POST).
 * @param array $inputLabels Labels for each input.
 * @param string $inputType Input type (e.g. text, number).
 * @return string HTML code of the form.
 */
function generateForm(string $action, string $method = 'POST', array $inputLabels, string $inputType): string {
    $form = "<form action=\"{$action}\" method=\"{$method}\">";

    foreach ($inputLabels as $textLabel) {
        $normalizedLabel = normalize_input($textLabel);
        $form .= "<label>{$textLabel}</label>";
        $form .= "<input type=\"{$inputType}\" name=\"{$normalizedLabel}\">";
    }

    return "{$form}<input type=\"submit\"></form>";
}

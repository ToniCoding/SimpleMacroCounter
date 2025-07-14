<?php

require_once "app/helpers/userInputs.php";

function htag_p($text): string {
    return "<p>{$text}</p>";
}

function generateForm(string $action, string $method = 'POST', array $inputLabels , string $inputType): string {
    $form = "<form action=\"{$action}\" method = \"{$method}\">";

    foreach ($inputLabels as $textLabel) {
        $normalizedLabel = normalize_input($textLabel);
        $form .= "<label>{$textLabel}</label>";
        $form .= "<input type=\"{$inputType}\" name = \"{$normalizedLabel}\">";
    }

    return "{$form}<input type=\"submit\"></form>";
}

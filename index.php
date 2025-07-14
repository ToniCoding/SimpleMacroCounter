<?php

require_once __DIR__ . "/app/controller/MacroCounterController.php";

$macroController = new MacroCounterController();
$macros = $macroController->createMacros(["protein","carbs", "fat"], [140, 150, 64], [120, 150, 60]);

$macroController->displayMacrosAndCalories($macros);
$macroController->displayIngestedMacrosForm();
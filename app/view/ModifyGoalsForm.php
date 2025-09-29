<?php
    namespace App\View;

    session_start();

    $formTkn = bin2hex(random_bytes(32));
    $_SESSION['modGoalFormTkn'] = $formTkn;

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" href="public/img/favicon.ico" type="image/x-icon">
        <title>Modify macro goal - SMC</title>
    </head>
    <body>
        <h3>Modify macro goal</h3>
        <form action="/modGoals" method="post">
            <input type="hidden" name="modGoalFormTkn" value="<?= htmlspecialchars($formTkn) ?>">
            <input type="hidden" name="modAction" value="modGoals">
        
            <label for="macroName">Macro goal to modify &rightarrow;</label>
            <select name="macroName" id="macroName">
                <option value="protein">Protein</option>
                <option value="carbs">Carbs</option>
                <option value="fats">Fats</option>
            </select><br/><br/>
            
            <label for="macroGoal">New goal &rightarrow;</label>
            <input type="text" name="macroGoal" id="macroGoal"><br/><br/>

            <button type="submit">Save changes</button>
        </form>
        <hr>
        <h3>Add macronutrients consumed</h3>
        <p><i>Note: Macros setted to 0 will be ignored.</i></p>
        <form action="/modGoals" method="post">
            <input type="hidden" name="modGoalFormTkn" value="<?= htmlspecialchars($formTkn) ?>">
            <input type="hidden" name="modAction" value="modMacros">
        
            <label for="proteins">Protein</label>
            <input type="text" name="proteins" id="proteins" placeholder="Amount of macronutrient to add">

            <label for="carbs">Carbs</label>
            <input type="text" name="carbs" id="carbs" placeholder="Amount of macronutrient to add">
            
            <label for="fats">Fats</label>
            <input type="text" name="fats" id="fats" placeholder="Amount of macronutrient to add">

            <button type="submit">Save changes</button>
        </form>
    </body>
</html>

<?php

    session_start();

    $formTkn = bin2hex(random_bytes(32));
    $_SESSION['modGoalFormTkn'] = $formTkn;

    echo $formTkn;
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" href="public/imag/favicon.ico" type="image/x-icon">
        <title>Modify macro goal - SMC</title>
    </head>
    <body>
        <form action="/modGoals" method="post">
            <input type="hidden" name="modGoalFormTkn" value="<?= htmlspecialchars($formTkn) ?>">
        
            <label for="macroName">Macro goal to modify &rightarrow;</label>
            <select name="macroName" id="macroName">
                <option value="protein">Protein</option>
                <option value="carbs">Carbs</option>
                <option value="fats">Fats</option>
            </select><br/><br/>
            
            <label for="macroGoal">New goal &rightarrow;</label>
            <input type="text" name="macroGoal" id="macroGoal"><br/><br/>

            <button type="submit">Submit</button>
        </form>
    </body>
</html>

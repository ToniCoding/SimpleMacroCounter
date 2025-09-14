<?php
    session_start();

    $formTkn = bin2hex(random_bytes(32));
    $_SESSION['registerFormTkn'] = $formTkn;
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Document</title>
    </head>
    <body>
        <form action="/regprocess" method="post">
            <input type="hidden" name="action" value="register">
            <input type="hidden" name="registerFormTkn" value="<?= htmlspecialchars($formTkn) ?>">
        
            <label for="username">Username</label>
            <input type="text" name="username" id="username">
        
            <label for="password">Password</label>
            <input type="password" name="password" id="password">
        
            <label for="email">Email</label>
            <input type="email" name="email" id="email">
        
            <label for="alias">Alias</label>
            <input type="text" name="alias" id="alias">
        
            <label for="age">Age</label>
            <input type="text" name="age" id="age">
            <button type="submit">Submit</button>
        </form>
    </body>
</html>

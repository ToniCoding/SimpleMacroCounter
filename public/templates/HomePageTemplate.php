<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" href="public/imag/favicon.ico" type="image/x-icon">
        <title>Home - SMC</title>
    </head>
    <body>
        <h3>Welcome to SMC main page <?= $username['username'] ?></h3>
        <form action="/logout" method="post"> 
           <input type="hidden" name="action" value="Logout">
           <input type="hidden" name="logoutFormTkn" value="<?= htmlspecialchars($logoutFormTkn) ?>">
           <input type="submit" value="Logout">
        </form>
        <?= $combinedController->displayMacrosTable($consumedMacros, $goalMacros, $userId) ?>
        <button onclick="window.location.href='/modifyGoals';">Modify macro goals</button>
    </body>
</html>

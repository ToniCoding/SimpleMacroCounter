<?php
    session_start();

    $logoutFormTkn = bin2hex(random_bytes(32));
    $_SESSION['logoutFormTkn'] = $logoutFormTkn;

    if (array_key_exists('status', $_GET) && $_GET['status'] == "success") {
        echo "Successfully logged in.";
    };

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" href="public/imag/favicon.ico" type="image/x-icon">
        <title>Home - Simple Macro Counter</title>
    </head>
    <body>
        <?php
            if (!array_key_exists('auth_token', $_COOKIE) || $_COOKIE['auth_token'] == null) {
                header('Location: /login');
                exit;
            }

            $username = $globalContainer->getService('userRepository')->getByAuthToken($_COOKIE['auth_token']);
            echo "<h3>Welcome to SMC main page " . $username['username'] . "</h3>";
        ?>
        <form action="/logout" method="post"> 
           <input type="hidden" name="action" value="Logout">
           <input type="hidden" name="logoutFormTkn" value="<?= htmlspecialchars($logoutFormTkn) ?>">
           <input type="submit" value="Logout">
        </form>
        <?php
            $macroContainer = new MacroContainer($globalContainer);
            $userId = $globalContainer->getService('userRepository')->findUserIdByName($username['username'])[0]['id'];
            $combinedController = $macroContainer->getCombinedMacroController();
            $macroController = $macroContainer->getMacroController();
            $consumedMacros = $combinedController->getMacroData($userId);
            $goalMacros = $combinedController->getMacroGoal($userId);
            
            echo $combinedController->displayMacrosTable($consumedMacros, $goalMacros, $userId);
        ?>
        <button onclick="window.location.href='/modifyGoals';">Modify macro goals</button>
    </body>
</html>

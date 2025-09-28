<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" href="public/imag/favicon.ico" type="image/x-icon">
        <title>Login - SMC</title>
    </head>
    <body>
        <form action="/regprocess" method="post">
            <input type="hidden" name="action" value="login">
            <input type="hidden" name="loginFormTkn" value="<?= htmlspecialchars($formTkn) ?>">
        
            <label for="username">Username</label>
            <input type="text" name="username" id="username">
        
            <label for="password">Password</label>
            <input type="password" name="password" id="password">

            <button type="submit">Login</button>
        </form>
    </body>
</html>
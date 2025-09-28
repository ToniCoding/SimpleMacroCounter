<?php

namespace App\Auth;

use App\Logging\Logger;

/**
 * Validate the form token.
 * 
 * - Check that the token sent via POST exists.
 * - Check that the token stored in the session exists.
 * - Verify that both tokens match using hash_equals().
 * 
 * This verifications will avoid Cross-Site Request Forgery (CSRF) and also avoid resubmissions.
 */

session_start();

/** @var Auth $login */
$login = $globalContainer->getService('auth');

/** @var Logger $log */
$log = $globalContainer->getService('logger');

$formAction = strtolower($_POST['action']);

if($formAction == 'register') {
    $formTknRequisites = empty($_SESSION['registerFormTkn'])
                        || !isset($_POST['registerFormTkn'])
                        || !hash_equals($_SESSION['registerFormTkn'], $_POST['registerFormTkn']);

    if ($formTknRequisites) {
        http_response_code(400);
        exit('Invalid request');
    }

    unset($_SESSION['registerFormTkn']);
}

if($formAction == 'logout') {
    $formTknRequisites = empty($_SESSION['logoutFormTkn'])
                        || !isset($_POST['logoutFormTkn'])
                        || !hash_equals($_SESSION['logoutFormTkn'], $_POST['logoutFormTkn']);

    if ($formTknRequisites) {
        http_response_code(400);
        exit('Invalid request');
    }

    unset($_SESSION['logoutFormTkn']);
}



if($formAction == 'register') {
    if(!$login->register($_POST)) {
        echo 'There was a problem registering the user, try again later.';
    } else {
        echo 'Successfully registered the user.';
    }
}

if($formAction == 'login') {
    if(!$login->login($_POST)) {
        header('Location: /login?status=failed');
    } else {
        header('Location: /home?status=success');
    }
}

if($formAction == 'logout') {
    $log->info('Executed logout action');
    $login->logout();
    
    echo 'Logged out successfully';
}

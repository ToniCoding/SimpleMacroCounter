<?php

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

$formTknRequisites = empty($_SESSION["registerFormTkn"])
                    || !isset($_POST["registerFormTkn"])
                    || !hash_equals($_SESSION["registerFormTkn"], $_POST["registerFormTkn"]);

if ($formTknRequisites) {
    http_response_code(400);
    exit("Invalid request");
}

unset($_SESSION["registerFormTkn"]);

/** @var Auth $login */
$login = $globalContainer->getService('auth');

if($_POST['action'] == "register") {
    if(!$login->register($_POST)) {
        echo "There was a problem registering the user, try again later.";
    } else {
        echo "Successfully registered the user.";
    }
}

if($_POST['action'] == "login") {
    if(!$login->login($_POST)) {
        echo "Incorrect email or password.";
    } else {
        echo "Logged in successfully.";
    }
}

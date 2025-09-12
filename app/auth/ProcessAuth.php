<?php

/** @var Auth $login */
$login = $globalContainer->getService('auth');

if($_POST['action'] == "register") {
    if(!$login->register($_POST)) {
        echo "There was a problem trying to register the user.";
    } else {
        echo "User registered successfully.";
    }
}

if($_POST['action'] == "login") {
    if(!$login->login($_POST)) {
        echo "Wrong credentials";
    } else {
        echo "Logged in successfully.";
    }
}

<?php

    namespace Public\Pages;

    session_start();

    $formTkn = bin2hex(random_bytes(32));
    $_SESSION['loginFormTkn'] = $formTkn;

    if (array_key_exists('status', $_GET) && $_GET['status'] == 'failed') {
        echo 'Incorrect email or password';
    };

    class LoginPage {
        public function renderPage($globalContainer): void {
            require_once BASE_PATH . 'public/templates/LoginPageTemplate.php';
        }
    }

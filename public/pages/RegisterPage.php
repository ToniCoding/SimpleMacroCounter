<?php
    session_start();

    function renderPage($globalContainer): void {
        $formTkn = bin2hex(random_bytes(32));
        $_SESSION['registerFormTkn'] = $formTkn;
        require_once BASE_PATH . 'public/templates/RegisterPageTemplate.php';
    }

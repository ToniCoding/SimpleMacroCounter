<?php
    session_start();

    if (!array_key_exists('auth_token', $_COOKIE) || $_COOKIE['auth_token'] == null) {
        header('Location: /login');
        exit;
    }

    $formTknRequisites = empty($_SESSION['modGoalFormTkn'])
                        || !isset($_POST['modGoalFormTkn'])
                        || !hash_equals($_SESSION['modGoalFormTkn'], $_POST['modGoalFormTkn']);

    if ($formTknRequisites) {
        http_response_code(400);
        exit('Invalid request');
    }

    unset($_SESSION['modGoalFormTkn']);

    function renderPage($globalContainer): void {
        $userRepository = $globalContainer->getService('userRepository');
        $userGoalsRepository = $globalContainer->getService('userGoalsRepository');
        $caloriesIntakeRepository = $globalContainer->getService('caloriesIntakeRepository');
        $username = $userRepository->getByAuthToken($_COOKIE['auth_token']);
        $modAction = $_POST['modAction'];
        $formDataInvoker = new ModifyGoalsFormInvoker($userRepository, $caloriesIntakeRepository, $userGoalsRepository);
        
        if ($modAction == 'modGoals' && $formDataInvoker->handleModGoalsData($_POST)) {
            $modResult = 'Successfuly updated the macro goal!';
        }
    
        if ($modAction == 'modMacros' && $formDataInvoker->handleMacroConsumed($_POST)) {
            $modResult = 'Successfully updated the consumed macros!';
        }

        require_once BASE_PATH . 'public/templates/ModGoalsPageTemplate.php';
    }

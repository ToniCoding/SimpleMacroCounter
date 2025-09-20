<?php
    session_start();

    if (!array_key_exists('auth_token', $_COOKIE) || $_COOKIE['auth_token'] == null) {
        header('Location: /login');
        exit;
    }

    echo $_SESSION["modGoalFormTkn"] . "<br/>";
    echo $_POST["modGoalFormTkn"] . "<br/>";

    $formTknRequisites = empty($_SESSION["modGoalFormTkn"])
                        || !isset($_POST["modGoalFormTkn"])
                        || !hash_equals($_SESSION["modGoalFormTkn"], $_POST["modGoalFormTkn"]);

    if ($formTknRequisites) {
        http_response_code(400);
        exit("Invalid request");
    }

    unset($_SESSION["modGoalFormTkn"]);

    $userRepository = $globalContainer->getService('userRepository');
    $userGoalsRepository = $globalContainer->getService('userGoalsRepository');
    $username = $userRepository->getByAuthToken($_COOKIE['auth_token']);
    $formDataInvoker = new ModifyGoalsFormInvoker($userRepository, $userGoalsRepository);
    $formDataInvoker = $formDataInvoker->handleModGoalsData($_POST);

    if ($formDataInvoker) {
        echo "Successfuly updated the macro goal!";
    }
?>

<button onclick="window.location.href='/';">Back to home</button>

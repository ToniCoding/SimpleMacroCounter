<?php
    namespace Public\Pages;

    use App\Containers\MacroContainer;
    use App\Exceptions\NoRecordFoundException;

    session_start();

    if (array_key_exists('status', $_GET) && $_GET['status'] == 'success') {
        echo 'Successfully logged in.';
    };

    if (!array_key_exists('auth_token', $_COOKIE) || $_COOKIE['auth_token'] == null) {
        header('Location: /login');
        exit;
    }

    class HomePage {
        public function renderPage($globalContainer): void {
            $log = $globalContainer->getService('logger');
            
            try {    
                $username = $globalContainer->getService('userRepository')->getByAuthToken($_COOKIE['auth_token']);
                $userId = $globalContainer->getService('userRepository')->findUserIdByName($username['username'])[0]['id'];
                $macroContainer = new MacroContainer($globalContainer);
                $combinedController = $macroContainer->getCombinedMacroController();
                $consumedMacros = $combinedController->getMacroData($userId);
                $goalMacros = $combinedController->getMacroGoal($userId);
                $logoutFormTkn = bin2hex(random_bytes(32));
                $_SESSION['logoutFormTkn'] = $logoutFormTkn;

            } catch (NoRecordFoundException) {
                $log->error('No record found for the query in database.');
                $goalMacros = [];
                echo 'Can not find your data. Please, contact SMC developer or open an issue in GitHub.';
            }
            
            require_once BASE_PATH . 'public/templates/HomePageTemplate.php';
        }
    }

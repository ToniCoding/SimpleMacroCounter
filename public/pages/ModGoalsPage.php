<?php
    namespace Public\Pages;

    use App\Invoker\ModifyGoalsFormInvoker;
    use App\Exceptions\ExceededMacroLimitException;

    use LengthException;

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

    class ModGoalsPage {
        public function renderPage($globalContainer): void {
            $userRepository = $globalContainer->getService('userRepository');
            $userGoalsRepository = $globalContainer->getService('userGoalsRepository');
            $caloriesIntakeRepository = $globalContainer->getService('caloriesIntakeRepository');
            $username = $userRepository->getByAuthToken($_COOKIE['auth_token']);
            $modAction = $_POST['modAction'];
            $formDataInvoker = new ModifyGoalsFormInvoker($userRepository, $caloriesIntakeRepository, $userGoalsRepository);

            try {
                $actionsHandled = [
                    'modGoals' => fn($data): bool => $formDataInvoker->handleModGoalsData($data),
                    'modMacros' => fn($data): bool => $formDataInvoker->handleMacroConsumed($data)
                ];

                $actionMessages = [
                    'modGoals' => 'Successfully updated the macro goal!',
                    'modMacros' => 'Successfully updated the consumed macros!'
                ];

                if (isset($actionsHandled[$modAction]) && $actionsHandled[$modAction]($_POST)) {
                    $modResult = $actionMessages[$modAction];
                }

            } catch (LengthException $ex) {
                $modResult = 'Numbers longer than 4 characters are not allowed.';
            } catch (ExceededMacroLimitException $ex) {
                $modResult = $ex->getMessage();
            }

            require_once BASE_PATH . 'public/templates/ModGoalsPageTemplate.php';
        }
    }

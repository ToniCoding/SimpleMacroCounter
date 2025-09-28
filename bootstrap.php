<?php

// App constants.
require_once __DIR__ . '/AppConstants.php';

// Configurations.
require_once BASE_PATH . '/config/ObjectFactories.php';
require_once BASE_PATH . '/config/Services.php';

// Models.
require_once BASE_PATH . 'app/model/Macro.php';
require_once BASE_PATH . 'app/model/CombinedMacros.php';
require_once BASE_PATH . 'app/model/MacrosCounter.php';
require_once BASE_PATH . 'app/model/Streak.php';
require_once BASE_PATH . 'app/model/User.php';

// Loggers and helpers.
require_once BASE_PATH . 'app/logging/Logger.php';
require_once BASE_PATH . 'app/helpers/HtmlHelper.php';
require_once BASE_PATH . 'app/helpers/UserInputs.php';
require_once BASE_PATH . 'app/helpers/DateParser.php';
require_once BASE_PATH . 'app/helpers/CaloriesCalculator.php';

// Repositories.
require_once BASE_PATH . 'app/repository/CaloriesIntakeRepository.php';
require_once BASE_PATH . 'app/repository/MetricsRepository.php';
require_once BASE_PATH . 'app/repository/UserGoalsRepository.php';
require_once BASE_PATH . 'app/repository/UserRepository.php';

// Handlers and services.
require_once BASE_PATH . 'app/handlers/UserFormHandler.php';
require_once BASE_PATH . 'app/handlers/MacroFormHandler.php';

// Controllers.
require_once BASE_PATH . 'app/controller/MacroController.php';
require_once BASE_PATH . 'app/controller/CombinedMacroController.php';
require_once BASE_PATH . 'app/controller/UserController.php';
require_once BASE_PATH . 'app/controller/StreakController.php';

// Views.
require_once BASE_PATH . 'app/view/MacroCounterView.php';

// DB connections.
require_once BASE_PATH . '/config/DbConnector.php';

// Authenticate.
require_once BASE_PATH . 'app/auth/AuthService.php';
require_once BASE_PATH . 'app/auth/Auth.php';

// Containers.
require_once BASE_PATH . 'app/containers/MacroContainer.php';

// Invokers.
require_once BASE_PATH . 'app/invoker/UserFormInvoker.php';
require_once BASE_PATH . 'app/invoker/ModifyGoalsFormInvoker.php';

// Index router.
require_once BASE_PATH . 'index.php';

/** @var DateParser $dateParser */
$dateParser = $globalContainer->getService('dateParser');

/** @var Logger $logger */
$logger = $globalContainer->getService('logger');

/** @var DbConnection $dbConnection */
$dbConnection = $globalContainer->getService('db');

/** @var AuthService $auth */
$auth = $globalContainer->getService('authService');

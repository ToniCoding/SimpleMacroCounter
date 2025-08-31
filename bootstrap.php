<?php

require_once __DIR__ . "/AppConstants.php";

// Configurations.
require_once BASE_PATH . "/config/ObjectFactories.php";
require_once BASE_PATH . "/config/Services.php";

// Models.
require_once BASE_PATH . "app/model/Macro.php";
require_once BASE_PATH . "app/model/MacrosCounter.php";
require_once BASE_PATH . "app/model/Streak.php";
require_once BASE_PATH . "app/model/User.php";

// Loggers and helpers.
require_once BASE_PATH . "app/logging/Logger.php";
require_once BASE_PATH . 'app/helpers/htmlHelper.php';
require_once BASE_PATH . 'app/helpers/dateParser.php';

// Repositories.
require_once BASE_PATH . "app/repository/UserRepository.php";

// Handlers and services.
require_once BASE_PATH . "app/handlers/UserFormHandler.php";

// Controllers.
require_once BASE_PATH . "app/controller/MacroCounterController.php";
require_once BASE_PATH . "app/controller/UserController.php";
require_once BASE_PATH . "app/controller/StreakController.php";

// Views.
require_once BASE_PATH . "app/view/MacroCounterView.php";

// DB connections.
require_once BASE_PATH . "/config/db.php";

// Authenticate.
require_once BASE_PATH . "app/auth/AuthService.php";
require_once BASE_PATH . "app/auth/Auth.php";

// Invokers.
require_once BASE_PATH . "app/invoker/UserFormInvoker.php";


/** @var DateParser $dateParser */
$dateParser = $globalContainer->getService("dateParser");

/** @var Logger $logger */
$logger = $globalContainer->getService("logger");

/** @var DbConnection $dbConnection */
$dbConnection = $globalContainer->getService("db");

/** @var AuthService $auth */
$auth = $globalContainer->getService('auth');

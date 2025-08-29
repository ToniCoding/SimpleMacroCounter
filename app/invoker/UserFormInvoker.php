<?php

require_once __DIR__ . "../../AppConstants.php";

require_once BASE_PATH . "config.php";
require_once BASE_PATH . "app/controller/UserController.php";
require_once BASE_PATH . "app/handlers/UserFormHandler.php";
require_once BASE_PATH . "app/repository/UserRepository.php";
require_once BASE_PATH . "bootstrap.php";

$userRepository = new UserRepository($dbConnection->connect());
$userFormHandler = new UserFormHandler($dateParser);
$userController = new UserController($dbConnection, $userRepository, $userFormHandler);

$userController->createUser($userFormHandler->handle($_POST));

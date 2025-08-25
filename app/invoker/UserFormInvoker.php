<?php

require_once __DIR__ . "/../../config.php";

require_once BASE_PATH . "app/controller/UserController.php";
require_once BASE_PATH . "app/handlers/UserFormHandler.php";
require_once BASE_PATH . "app/repository/UserRepository.php";
require_once BASE_PATH . "app/logging/Logger.php";
require_once BASE_PATH . "config/db.php";

// !!! THIS MUST BE OBTAINED THROUGH ENV VARS OR PROPERTIES --- EXPERIMENTAL CREDENTIALS !!!
$serverName = "localhost";
$username = "op_user";
$password = "1234";
$databaseName = "smc";
$dsn = "mysql:host=$serverName;dbname=$databaseName;charset=utf8";

$dateParser = new DateParser();
$logger = new Logger($dateParser);
$dbConnection = new DbConnection($serverName, $username, $password, $databaseName, $logger);
$userRepository = new UserRepository($dbConnection->connect());
$userFormHandler = new UserFormHandler($dateParser);
$userController = new UserController($dbConnection, $userRepository, $userFormHandler);

$userController->createUser($userFormHandler->handle($_POST));

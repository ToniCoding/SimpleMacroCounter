<?php

require_once __DIR__ . "/../../bootstrap.php";

class UserFormInvoker {
    private UserRepository $userRepository;
    private UserFormHandler $userFormHandler;
    private UserController $userController;
    private Logger $logger;

    public function __construct(UserRepository $userRepository, UserFormHandler $userFormHandler, UserController $userController, $container) {
        $this->userRepository = $userRepository;
        $this->userFormHandler = $userFormHandler;
        $this->userController = $userController;
        $this->logger = $container->getService("logger");
    }

    public function handleUserCreation(array $postData): bool {
        try {
            $this->userController->createUser($this->userFormHandler->handle($postData));
            return true;
        } catch (Exception $ex) {
            $this->logger->info("[UserFormInvoker] An exception happened:\n $ex");
            return false;
        }
    }
}

$userRepository = new UserRepository($dbConnection->connect());
$userFormHandler = new UserFormHandler($dateParser);
$userController = new UserController($dbConnection, $userRepository, $userFormHandler);

$userFormInvoker = new UserFormInvoker($userRepository, $userFormHandler, $userController, $container);
$userFormInvoker->handleUserCreation($_POST);

<?php

require_once __DIR__ . "/../../bootstrap.php";

class UserFormInvoker {
    private UserRepository $userRepository;
    private UserFormHandler $userFormHandler;
    private UserController $userController;
    private Logger $logger;

    public function __construct(Container $globalContainer, UserRepository $userRepository, UserFormHandler $userFormHandler, UserController $userController) {
        $this->userRepository = $userRepository;
        $this->userFormHandler = $userFormHandler;
        $this->userController = $userController;
        $this->logger = $globalContainer->getService("logger");
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

$userRepository = new UserRepository($globalContainer->getService("db")->connect());
$userFormHandler = new UserFormHandler($globalContainer->getService("dateParser"));
$userController = new UserController(
    $globalContainer->getService("db"),
    $userRepository
);

$userFormInvoker = new UserFormInvoker($globalContainer, $userRepository, $userFormHandler, $userController);
$userFormInvoker->handleUserCreation($_POST);

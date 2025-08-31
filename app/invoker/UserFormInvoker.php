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

    public function handleUserCreation(User $user): bool {
        try {
            $this->userController->createUser($user);
            return true;
        } catch (Exception $ex) {
            $this->logger->info("[UserFormInvoker] An exception happened:\n $ex");
            return false;
        }
    }
}

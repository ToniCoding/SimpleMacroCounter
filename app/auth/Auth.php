<?php

class Auth {
    private Logger $logger;
    private AuthService $authService;

    private Container $globalContainer;
    private Container $authContainer;

    public function __construct(Container $globalContainer) {
        $this->globalContainer = $globalContainer;
        $this->authContainer = new Container();

        $this->logger = $globalContainer->getService('logger');
        $this->authService = $globalContainer->getService('auth');
    }

    public function register(array $postData): bool {
        $this->setServices('register', $this->globalContainer, $this->authContainer);

        $userFormHandler = $this->authContainer->getService('userFormHandler');
        $userController = $this->authContainer->getService('userController');

        $user = $userFormHandler->handle($postData);
        $userController->createUser($user);

        $this->authService->loginTkn($user->getId());

        return true;
    }

    public function login(array $postData): bool {
        $this->setServices('login', $this->globalContainer, $this->authContainer);

        $userRepository = $this->authContainer->getService('userRepository');
        $user = $userRepository->findUserIdByName($postData['username'] ?? '');
        $userId = $user[0]["id"];

        if (!$user) return false;
        if (!$userRepository->checkPassword($userId, $postData['password'])) return false;

        $this->authService->loginTkn($userId);

        return true;
    }

    // Need check...
    public function logout(): void {
        $userId = $this->authService->checkAuthTkn();
        if ($userId) {
            $this->authService->logoutTkn($userId);
        }
    }

    private function setServices(string $action, $globalContainer, $authContainer): void {
        $this->authContainer->setService('userRepository', function() use ($globalContainer): UserRepository{
            return new UserRepository($globalContainer->getService('db')->connect());
        });

        $this->authContainer->setService('userController', function() use ($authContainer, $globalContainer): UserController {
            return new UserController(
                $globalContainer->getService('db')->connect(),
                $authContainer->getService('userRepository'));
        });

        if($action == "register") {
            $this->authContainer->setService('userFormHandler', function() use ($globalContainer): UserFormHandler {
                return new UserFormHandler($globalContainer->getService('dateParser'));
            });
        }
    }
}

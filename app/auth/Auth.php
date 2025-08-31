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
        $this->authService = $globalContainer->getService('authService');
    }

    public function register(array $postData): bool {
        $this->setServices('register', $this->globalContainer, $this->authContainer);

        /** @var UserFormHandler $userFormHandler */
        $userFormHandler = $this->authContainer->getService('userFormHandler');

        /** @var UserController $userController */
        $userController = $this->authContainer->getService('userController');

        /** @var UserFormInvoker $userFormInvoker */
        $userFormInvoker = $this->authContainer->getService('userFormInvoker');

        /**
         * Service cascade:
         *      - Creates the user object.
         *      - Creates the user at database level.
         *      - Gives the user an auth token.
         */
        /** @var User $user */
        $user = $userFormHandler->handle($postData);
        $userFormInvoker->handleUserCreation($user);
        $userId = $userController->retrieveUserByUsername($user->getUsername())[0]['id'];
        $this->authService->loginTkn($userId); // User object does not have the id yet.

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
                $authContainer->getService('userRepository'));
        });

        if($action == "register") {
            $this->authContainer->setService('userFormHandler', function() use ($globalContainer): UserFormHandler {
                return new UserFormHandler($globalContainer->getService('dateParser'));
            });

            $userRepository = $this->authContainer->getService('userRepository');
            $userController = $this->authContainer->getService('userController');
            $userFormHandler = $this->authContainer->getService('userFormHandler');

            $this->authContainer->setService('userFormInvoker', function() use ($globalContainer, $userRepository, $userFormHandler, $userController): UserFormInvoker {
                return new UserFormInvoker($globalContainer, $userRepository, $userFormHandler, $userController);
            });
        }
    }
}

<?php

require_once 'app/model/User.php';
require_once 'app/repository/UserRepository.php';
require_once 'config/db.php';

class UserController {
    private UserRepository $userRepository;
    private DbConnection $dbConnection;

    private string $serverName = "localhost";
    private string $username = "op_user";
    private string $password = "1234";
    private string $databaseName = "smc";

    public function __construct() {
        $this->dbConnection = new DbConnection($this->serverName, $this->username, $this->password, $this->databaseName); // This arguments must come from a secrets file or in the moment controller is invoked.
        $this->userRepository = new UserRepository($this->dbConnection->connect());
    }

    public function createUser(User $user): bool {
        return $this->userRepository->create($user);
    }

    public function retrieveUser(User $user) {
        return $this->userRepository->findUserById($user);
    }
}

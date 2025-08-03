<?php

require_once 'app/model/User.php';
require_once 'app/repository/UserRepository.php';
require_once 'config/db.php';

/**
 * Class UserController
 * Controller that manages user CRUD operations using UserRepository and database connection.
 */
class UserController {
    private UserRepository $userRepository;
    private DbConnection $dbConnection;

    private string $serverName = "localhost";
    private string $username = "op_user";
    private string $password = "1234";
    private string $databaseName = "smc";

    /**
     * Constructor that initializes the database connection and repository.
     */
    public function __construct() {
        $this->dbConnection = new DbConnection($this->serverName, $this->username, $this->password, $this->databaseName);
        $this->userRepository = new UserRepository($this->dbConnection->connect());
    }

    /**
     * Creates a user in the database.
     *
     * @param User $user User instance.
     * @return bool Operation result.
     */
    public function createUser(User $user): bool {
        return $this->userRepository->create($user);
    }

    /**
     * Deletes a user from the database.
     *
     * @param User $user User instance.
     * @return bool Operation result.
     */
    public function deleteUser(User $user): bool {
        return $this->userRepository->delete($user);
    }

    /**
     * Edits a specific field of the user.
     *
     * @param User $user User instance.
     * @param string $field Field name to edit.
     * @param int $age New value for the field.
     * @return bool Operation result.
     */
    public function editUser(User $user, string $field, int $age): bool {
        return $this->userRepository->edit($user, $field, $age);
    }

    /**
     * Retrieves a user by ID.
     *
     * @param User $user User instance.
     * @return mixed Search result.
     */
    public function retrieveUser(User $user) {
        return $this->userRepository->findUserById($user);
    }
}

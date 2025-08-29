<?php

require_once __DIR__ . "../../AppConstants.php";

require_once BASE_PATH . "config.php";
require_once BASE_PATH . 'app/model/User.php';
require_once BASE_PATH . 'app/repository/UserRepository.php';
require_once BASE_PATH . 'config/db.php';

/**
 * Class UserController
 * Controller that manages user CRUD operations using UserRepository and database connection.
 */
class UserController {
    private DbConnection $dbConnection;
    private UserRepository $userRepository;
    private UserFormHandler $userFormHandler;

    private string $serverName = "localhost";
    private string $username = "op_user";
    private string $password = "1234";
    private string $databaseName = "smc";

    /**
     * Constructor that initializes the database connection and repository.
     */
    public function __construct(DbConnection $dbConnection, UserRepository $userRepository, UserFormHandler $userFormHandler) {
        $this->dbConnection = $dbConnection;
        $this->userRepository = $userRepository;
        $this->userFormHandler = $userFormHandler;
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
     * @param string $newValue New value for the field.
     * @return bool Operation result.
     */
    public function editUser(User $user, string $field, string $newValue): bool {
        return $this->userRepository->edit($user, $field, $newValue);
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

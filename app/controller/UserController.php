<?php

/**
 * Class UserController
 * Controller that manages user CRUD operations using UserRepository and database connection.
 */
class UserController {
    private DbConnection $dbConnection;
    private UserRepository $userRepository;
    private UserFormHandler $userFormHandler;

    /**
     * Constructor that initializes the database connection and repository.
     */
    public function __construct(DbConnection $dbConnection, UserRepository $userRepository) {
        $this->dbConnection = $dbConnection;
        $this->userRepository = $userRepository;
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

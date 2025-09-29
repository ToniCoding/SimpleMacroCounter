<?php

namespace App\Controller;

use App\Model\User;
use App\Repository\UserRepository;
use App\Logging\Logger;
use App\Handlers\UserFormHandler;

/**
 * Class UserController
 * Controller that manages user CRUD operations using UserRepository and database connection.
 */
class UserController {
    private UserRepository $userRepository;
    private UserFormHandler $userFormHandler;
    private Logger $log;

    /**
     * Constructor that initializes the database connection and repository.
     */
    public function __construct(UserRepository $userRepository, Logger $log) {
        $this->userRepository = $userRepository;
        $this->log = $log;
    }

    /**
     * Creates a user in the database.
     *
     * @param User $user User instance.
     * @return bool Operation result.
     */
    public function createUser(User $user): bool {
        $this->log->info('Try creating a new user with given user object.');
        return $this->userRepository->create($user);
    }

    /**
     * Deletes a user from the database.
     *
     * @param User $user User instance.
     * @return bool Operation result.
     */
    public function deleteUser(User $user): bool {
        $this->log->info('Try deleting an existing user with given user object.');
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
        $this->log->info('Try editing an existing user with given user object.');
        return $this->userRepository->edit($user, $field, $newValue);
    }

    /**
     * Retrieves a user by ID.
     *
     * @param User $user User instance.
     * @return mixed Search result.
     */
    public function retrieveUser(User $user): array {
        $this->log->info('Try retrieving an user ID with user object.');
        return $this->userRepository->findUserById($user);
    }

    public function retrieveUserByUsername(string $username): array {
        $this->log->info('Try retrieving an user ID by username');
        return $this->userRepository->findUserIdByName($username);
    }
}

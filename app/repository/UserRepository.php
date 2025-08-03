<?php

require_once 'app/helpers/userInputs.php';

/**
 * Class UserRepository
 * Handles database operations related to User entities.
 */
class UserRepository {
    private PDO $connectionPDO;

    /**
     * Constructor injecting PDO connection.
     *
     * @param PDO $pdo Database connection.
     */
    public function __construct(PDO $pdo) {
        $this->connectionPDO = $pdo;
    }

    /**
     * Inserts a new user into the database. Then gets the user id and sets it to the user instance.
     *
     * @param User $user User entity.
     * @return bool True on success, false otherwise.
     */
    public function create(User $user): bool {
        $sqlStmt = $this->connectionPDO->prepare('INSERT INTO users (username, alias, email, password, age, status) VALUES (?, ?, ?, ?, ?, ?)');

        if ($sqlStmt->execute([
            $user->getUsername(),
            $user->getUserAlias(),
            $user->getEmail(),
            $user->getPassword(),
            $user->getAge(),
            $user->getIsActive() ? 1 : 0
        ])) {
            $sqlStmt = $this->connectionPDO->prepare('SELECT id FROM users WHERE username = :username;');
            $sqlStmt->execute(['username' => $user->getUsername()]);
            $user->setUserId($sqlStmt->fetch(PDO::FETCH_ASSOC)['id']);

            return true;
        }
    }

    /**
     * Deletes a user by ID.
     *
     * @param User $user User entity.
     * @return bool True on success, false otherwise.
     */
    public function delete(User $user): bool {
        $userId = $user->getUserId();
        $sqlStmt = $this->connectionPDO->prepare('DELETE FROM users WHERE id = :id');
        return $sqlStmt->execute(['id' => $userId]);
    }

    /**
     * Updates a specified field of a user.
     *
     * @param User $user User entity.
     * @param string $field Field name to update (username, password, age, email).
     * @param string $newValue New value for the field.
     * @return bool True on success, false otherwise.
     * @throws Exception On invalid field.
     */
    public function edit(User $user, string $field, string $newValue): bool {
        $userId = $user->getUserId();
        $validFields = ["username", "password", "age", "email"];

        if (!in_array($field, $validFields)) {
            throw new Exception("Invalid field.");
        }

        switch ($field) {
            case 'age':
                if (!check_integer($newValue)) {
                    return false;
                }
                $newValue = (int)$newValue;
                break;

            case 'email':
                if (!filter_var($newValue, FILTER_VALIDATE_EMAIL)) {
                    return false;
                }
                break;

            case 'username':
            case 'password':
                if (empty($newValue)) {
                    return false;
                }
                break;
        }

        $sql = "UPDATE users SET $field = :newValue WHERE id = :id";
        $sqlStmt = $this->connectionPDO->prepare($sql);

        return $sqlStmt->execute([
            'newValue' => $newValue,
            'id' => $userId
        ]);
    }

    /**
     * Finds a user by ID and returns basic info.
     *
     * @param User $user User entity.
     * @return array Result set with username, alias, email, and age.
     */
    public function findUserById(User $user): array {
        $userId = $user->getUserId();
        $sqlStmt = $this->connectionPDO->prepare('SELECT username, alias, email, age FROM users WHERE id = :id');

        $sqlStmt->execute(['id' => $userId]);
        return $sqlStmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

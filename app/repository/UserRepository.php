<?php

require_once 'app/helpers/userInputs.php';

class UserRepository {
    private PDO $connectionPDO;

    public function __construct(PDO $pdo) {
        $this->connectionPDO = $pdo;
    }

    public function create(User $user): bool {
        $sqlStmt = $this->connectionPDO->prepare('INSERT INTO users (username, alias, email, password, age, status) VALUES (?, ?, ?, ?, ?, ?)');

        return $sqlStmt->execute([
            $user->getUsername(),
            $user->getUserAlias(),
            $user->getEmail(),
            $user->getPassword(),
            $user->getAge(),
            $user->getIsActive() ? 1 : 0
        ]);
    }

    public function delete(User $user): bool {
        $userId = $user->getUserId();

        $sqlStmt = $this->connectionPDO->prepare('DELETE FROM users WHERE id = :id');
        
        if ($sqlStmt->execute(['id' => $userId])) {
            return true;
        }
        
        return false;
    }

    public function edit(User $user, string $field, string $newValue): bool {
        $userId = $user->getUserId();
        $validFields = array("username", "password", "age", "email");

        if (!in_array($field, $validFields)) {
            throw new Exception("Invalid fields.");
        }

        switch ($field) {
            case 'age': {
                if(!check_integer($newValue)) {
                    return false;
                }
                $newValue = (int)$newValue;
                break;
            }

            case 'email': {
                if (!filter_var($newValue, FILTER_VALIDATE_EMAIL)) {
                    return false;
                }
                break;
            }

            case 'username':
            case 'password':
                if (empty($newValue)) {
                    return false;
                }
                break;

            default:
                break;
        }

        $sql = "UPDATE users SET $field = :newValue WHERE id = :id";
        $sqlStmt = $this->connectionPDO->prepare($sql);

        return $sqlStmt->execute([
            'newValue' => $newValue,
            'id' => $userId
        ]);
    }

    public function findUserById(User $user): array {
        $userId = $user->getUserId();
        $sqlStmt = $this->connectionPDO->prepare('SELECT username, alias, email, age FROM users WHERE id = :id');

        $sqlStmt->execute(['id' => $userId]);
        $sqlResult = $sqlStmt->fetchAll(PDO::FETCH_ASSOC);

        return $sqlResult;
    }
}

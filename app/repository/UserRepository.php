<?php

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

    public function findUserById(User $user): array {
        $userId = $user->getUserId();
        $sqlStmt = $this->connectionPDO->prepare('SELECT username, alias, email, age FROM smc.users WHERE id = :id');

        $sqlStmt->execute(['id' => $userId]);
        $sqlResult = $sqlStmt->fetchAll(PDO::FETCH_ASSOC);

        return $sqlResult;
    }
}

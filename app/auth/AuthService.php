<?php

class AuthService {
    private PDO $dbConnection;

    public function __construct(DbConnection $dbConnection) {
        $this->dbConnection = $dbConnection->connect();
    }


    // Implement check password.
    public function loginTkn(int $userId): string {
        $authToken = bin2hex(random_bytes(32));

        $tokenStmt = $this->dbConnection->prepare("UPDATE users SET auth_token = ? WHERE id = ?");
        $tokenStmt->execute([$authToken, $userId]);

        setcookie("auth_token", $authToken, [
            "expires" => time() + 3600,
            "httponly" => true,
            "samesite" => "Strict"
        ]);

        return $authToken;
    }

    public function checkAuthTkn(): ?int {
        if (!isset($_COOKIE['auth_token'])) {
            return null;
        }

        $tokenStmt = $this->dbConnection->prepare("SELECT id FROM users WHERE token = ?");
        $tokenStmt->execute([$_COOKIE['auth_token']]);
        $userToken = $tokenStmt->fetch();

        return $userToken ? (int)$userToken['id'] : null;
    }

    public function logoutTkn(int $userId): void {
        $stmt = $this->dbConnection->prepare("UPDATE users SET token = NULL WHERE id = ?");
        $stmt->execute([$userId]);

        setcookie("auth_token", "", time() - 3600, "/");
    }
}

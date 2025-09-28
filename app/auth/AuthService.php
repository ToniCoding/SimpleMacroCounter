<?php

namespace App\Auth;

use Config\DbConnection;

use PDO;

class AuthService {
    private PDO $dbConnection;

    public function __construct(DbConnection $dbConnection) {
        $this->dbConnection = $dbConnection->connect();
    }

    public function loginTkn(int $userId): string {
        $authToken = bin2hex(random_bytes(32));
        $tokenExpires = date('Y-m-d H:i:s', time() + 3600);

        $tokenStmt = $this->dbConnection->prepare("UPDATE users SET auth_token = ?, token_expires = ? WHERE id = ?");
        $tokenStmt->execute([$authToken, $tokenExpires, $userId]);

        setcookie('auth_token', $authToken, [
            'expires' => time() + 3600,
            'httponly' => true,
            'samesite' => 'Strict'
        ]);

        return $authToken;
    }

    public function checkAuthTkn(): ?int {
        if (!isset($_COOKIE['auth_token'])) {
            return null;
        }
    
        $tokenStmt = $this->dbConnection->prepare(
            'SELECT id, token_expires FROM users WHERE auth_token = ?'
        );
        $tokenStmt->execute([$_COOKIE['auth_token']]);
        $userToken = $tokenStmt->fetch(PDO::FETCH_ASSOC);
    
        if (!$userToken) {
            return null;
        }
    
        if ($userToken['token_expires'] < time()) {
            return null;
        }
    
        return (int)$userToken['id'];
    }


    public function logoutTkn(int $userId): void {
        $stmt = $this->dbConnection->prepare("UPDATE users SET auth_token = NULL WHERE id = ?");
        $stmt->execute([$userId]);

        setcookie('auth_token', '', time() - 3600, '/');

        session_unset();
        session_destroy();
    }
}

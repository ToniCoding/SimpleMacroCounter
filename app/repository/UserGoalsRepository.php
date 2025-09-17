<?php

class UserGoalsRepository {
    private PDO $connectionPDO;
    private Logger $log;

    /**
     * Constructor injecting PDO connection and date parser.
     *
     * @param PDO $pdo Database connection.
     */
    public function __construct(PDO $pdo) {
        $this->connectionPDO = $pdo;
    }

    public function initializeUser(int $userId): bool {
        $sqlStmt = $this->connectionPDO->prepare("INSERT INTO user_goals(user_id, protein, carbs, fats) VALUES (?, 0, 0, 0)");
        
        return $sqlStmt->execute([$userId]);
    }

    public function getUserGoals(int $userId): array {
        $sqlStmt = $this->connectionPDO->prepare("SELECT protein, carbs, fats FROM user_goals WHERE user_id = :userId");
        if ($sqlStmt->execute(["userId" => $userId])) {
            return $sqlStmt->fetch(PDO::FETCH_ASSOC);
        }

        return [];
    }
}

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
        $sqlStmt = $this->connectionPDO->prepare('INSERT INTO user_goals(user_id, protein, carbs, fats) VALUES (?, 100, 250, 65)');
        
        return $sqlStmt->execute([$userId]);
    }

    public function getUserGoals(int $userId): array {
        $sqlStmt = $this->connectionPDO->prepare('SELECT protein, carbs, fats FROM user_goals WHERE user_id = :userId');
        if ($sqlStmt->execute(['userId' => $userId])) {
            return $sqlStmt->fetch(PDO::FETCH_ASSOC);
        }

        return [];
    }

    public function setUserGoal(int $userId, Macro $macro): bool {
        $allowed = ['protein', 'carbs', 'fats'];
        $column  = $macro->getMacroName();
        if (!in_array($column, $allowed)) {
            throw new InvalidArgumentException('Invalid macro');
        }

        $sql = "UPDATE user_goals SET `$column` = :goal WHERE user_id = :userId";
        $stmt = $this->connectionPDO->prepare($sql);
        if ($stmt->execute([
            ":goal"  => $macro->getMacroGoal(),
            ":userId"=> $userId
        ])){
            return true;
        };

        return false;
    }
}

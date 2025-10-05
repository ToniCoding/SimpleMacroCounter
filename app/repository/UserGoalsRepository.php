<?php

namespace App\Repository;

use App\Exceptions\NoRecordFoundException;
use App\Logging\Logger;
use App\Model\Macro;

use PDO, InvalidArgumentException, Exception;

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

    /**
     * Gets the user goals.
     * @param int $userId
     * @throws \App\Exceptions\NoRecordFoundException
     * @return array
     */
    public function getUserGoals(int $userId): array {
        $sqlStmt = $this->connectionPDO->prepare('SELECT protein, carbs, fats FROM user_goals WHERE user_id = :userId');
        if ($sqlStmt->execute(['userId' => $userId])) {
            $result = $sqlStmt->fetch(PDO::FETCH_ASSOC);

            if(!$result) {
                throw new NoRecordFoundException();
            }
        }

        return [];
    }

    /**
     * Sets the user goals.
     * @param int $userId
     * @param \App\Model\Macro $macro
     * @throws \InvalidArgumentException
     * @return bool
     */
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

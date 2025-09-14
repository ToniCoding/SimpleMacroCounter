<?php

/**
 * Class MetricsRepository
 * Handles database operations related to user metrics.
 */
class MetricsRepository {
    private PDO $connectionPDO;
    private Logger $log;

    /**
     * Constructor injecting PDO connection.
     *
     * @param PDO $pdo Database connection.
     */
    public function __construct(PDO $pdo) {
        $this->connectionPDO = $pdo;
    }

    /**
     * Initialize the user in the table. Often used in the register user service cascade.
     *
     * @param int $userId The user ID that will be initialized in the table.
     * @return bool True on success, false otherwise.
     */
    public function initializeUser(int $userId): bool {
        $sqlStmt = $this->connectionPDO->prepare("INSERT INTO user_metrics(user_id, creatine_streak, experience) VALUES (?, 0, 0)");
        
        if ($sqlStmt->execute([$userId])) {
            return true;
        }

        return false;
    }

    /**
     * Add day/s to the creatine streak.
     *
     * @param int $userId The user ID to increment its streak.
     * @param int $amount The amount of days to add.
     * @return bool True on success, false otherwise.
     */
    public function addCreatineStreak(int $userId, int $amount): bool {
        $sqlStmt = $this->connectionPDO->prepare("UPDATE user_metrics SET creatine_streak = creatine_streak + :daysToAdd WHERE user_id = :userId");
        if ($sqlStmt->execute([
            "daysToAdd" => $amount,
            "userId" => $userId
        ])) {
            return true;
        }
        return false;
    }

    /**
     * Add experience to the user.
     *
     * @param int $userId The user ID to increment its streak.
     * @param int $amount The amount of experience to add.
     * @return bool True on success, false otherwise.
     */
    public function addExperience(int $userId, int $amount): bool {
        $sqlStmt = $this->connectionPDO->prepare("UPDATE user_metrics SET experience = experience + :expToAdd WHERE user_id = :userId");
        
        if ($sqlStmt->execute([
            "expToAdd" => $amount,
            "userId" => $userId
        ])){
            return true;
        }

        return false;
    }
}

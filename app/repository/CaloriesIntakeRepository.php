<?php

class CaloriesIntakeRepository {
    private PDO $connectionPDO;
    private DateParser $dateParser;
    private Logger $log;

    /**
     * Constructor injecting PDO connection and date parser.
     *
     * @param PDO $pdo Database connection.
     * @param DateParser $dateParser Date parser helper.
     */
    public function __construct(PDO $pdo, DateParser $dateParser) {
        $this->connectionPDO = $pdo;
        $this->dateParser = $dateParser;
    }

    /**
     * Checks if the user have data in the date passed through arguments.
     *
     * @param int $userId The user ID to filter in the table.
     * @param DateTime $date The datetime to filter
     * @return bool True if there is data, false otherwise.
     */
    public function checkIfUserRegisteredIntake(int $userId, string $date): bool {
        $sqlStmt = $this->connectionPDO->prepare("SELECT kcals FROM kcals_daily WHERE user_id = :id AND `date` = :dayDate");
        $sqlStmt->execute([
            "id" => $userId,
            "dayDate" => $date]);

        return $sqlStmt->fetch(PDO::FETCH_ASSOC) != false;
    }

    /**
     * Initialize the user in the table. This will be triggered only if the user registers calories in the day.
     *
     * @param int $userId The user ID that will be initialized in the table.
     * @return bool True on success, false otherwise.
     */
    public function initializeDay(int $userId): bool {
        $currentDate = $this->dateParser->getDate("Y-m-d");
        $sqlStmt = $this->connectionPDO->prepare("INSERT INTO kcals_daily(user_id, date, kcals) VALUES (?, ?, 0)");
        
        if ($sqlStmt->execute([$userId, $currentDate])) {
            return true;
        }

        return false;
    }
}

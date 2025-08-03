<?php

require_once __DIR__ . '/../helpers/dateParser.php';
require_once __DIR__ . '/../model/User.php';
require_once __DIR__ . '/../repository/UserRepository.php';

class UserFormHandler {
    private PDO $dbConnection;
    private readonly DateParser $dateParser;

    public function __construct(PDO $dbConnection) {
        $this->dbConnection = $dbConnection;
        $this->dateParser = new DateParser();
    }

    public function handle(array $postData): bool {
        $username = trim($postData['username']);
        $password = trim($postData['password']);
        $email = trim($postData['email']);
        $alias = trim($postData['alias']);
        $age = (int)($postData['age']);

        if (!$username || !$password || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        $dateTime = $this->dateParser->getDate();
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $newUser = new User($username, $hashedPassword, $alias, $email, $age, $dateTime, $dateTime, true);

        $userRepository = new UserRepository($this->dbConnection);
        return $userRepository->create($newUser);
    }
}

<?php

require_once __DIR__ . "/../../config.php";

require_once BASE_PATH . 'app/helpers/dateParser.php';
require_once BASE_PATH . 'app/model/User.php';

class UserFormHandler {
    private readonly DateParser $dateParser;

    public function __construct() {
        $this->dateParser = new DateParser();
    }

    public function handle(array $postData): User {
        $username = trim($postData['username']);
        $password = trim($postData['password']);
        $email = trim($postData['email']);
        $alias = trim($postData['alias']);
        $age = (int)($postData['age']);

        if (!$username || !$password) {
            throw new Exception("Username and password must have a value");
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Incorrect mail format.");
        }

        $dateTime = $this->dateParser->getDate();
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $newUser = new User($username, $hashedPassword, $alias, $email, $age, $dateTime, $dateTime, true);

        return $newUser;
    }
}
